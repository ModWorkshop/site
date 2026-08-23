<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Http\Requests\FilteredRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Mod;
use App\Services\APIService;
use App\Services\ModService;
use Arr;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Models\Notification;
use App\Models\PendingFile;
use App\Models\Webhook;
use App\Services\Utils;
use Auth;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Storage;
use Str;
use z4kn4fein\SemVer\Version;

/**
 * @group Files
 */
class FileController extends Controller
{
    public function __construct() {
        $this->authorizeWithParentResource(File::class, Mod::class);
    }

    /**
     * List mod files
     *
     * Returns a list of files of a mod.
     */
    public function index(FilteredRequest $request, Mod $mod)
    {
        $val = $request->val([
            'version' => 'string|nullable',
            'prerelease' => 'boolean|nullable',
            'include_incomplete' => 'boolean|nullable',
        ]);

        return BaseResource::collectionResponse($mod->allFiles()->queryGet($val, function($query, $val) use ($mod) {
            $query->with('user');

            if (!empty($val['version'])) {
                // Check semver only if the query is valid semver
                if (Version::parseOrNull($val['version']) != null) {
                    $query->where('semver_version', $val['version'])->orWhere('version', $val['version']);
                } else {
                    $query->where('version', $val['version']);
                }
            }

            $preRelease = Arr::pull($val, 'prerelease', true);
            if (!$preRelease) {
                $query->whereRaw("(get_semver_prerelease (semver_version) = '') IS NOT FALSE");
            }

            $includeIncomplete = Arr::pull($val, 'include_incomplete', false);
            if (!$includeIncomplete) {
                $query->where("completed", true);
            }

            if ($mod->download_type == 'file' && isset($mod->download_id)) {
                $query->orderByRaw("(id = $mod->download_id) DESC, display_order DESC, semver_version IS NOT NULL DESC, semver_version DESC, updated_at DESC");
            } else {
                $query->orderByRaw("display_order DESC, semver_version IS NOT NULL DESC, semver_version DESC, updated_at DESC");
            }
        }));
    }

    public function fileBeginUpload(Request $request, File $file) {
        $mod = $file->mod;
        // Prevent replacing a file when losing supporter perks by limiting how much storage you can replace
        $remainingStorage = $mod->currentStorage + min($file->size, $mod->maxStorage);

        if ($remainingStorage < 0) {
            abort(403, "You don't have enough storage to upload this file");
        }

        $val = $request->validate([
            'name' => 'string|min_strict:1|max:100',
            'size' => "required|int|max:{$remainingStorage}"
        ]);

        return $this->createPendingFile($file->mod, $val['name'], $val['size'], $file);
    }

    public function createPendingFile(Mod $mod, string $name, int $size, File $file) {
        $fileType = Utils::safeFileType($name);

        $pendingFile = PendingFile::create([
            'name' => '',
            'file_type' => $fileType,
            'file_name' => $mod->id.'_'.Auth::user()->id.'_'.Str::random(40).(!empty($fileType) ? '.'.$fileType : ''),
            'user_id' => $this->user()->id,
            'size' => $size,
            "file_id" => $file?->id,
            "mod_id" => $mod->id
        ]);

        $tempUrl = Storage::disk('s3')->temporaryUploadUrl(
            'temp/'.$pendingFile->file_name, now()->addHours(6),
            [ 'ACL' => 'private' ]
        );

        unset($tempUrl['headers']['Host']);

        return [
            'id' => $pendingFile->id,
            'url' => $tempUrl['url'],
            'headers' => $tempUrl['headers']
        ];
    }

    public function completePendingFileUpload(PendingFile $pendingFile) {
        $disk = Storage::disk('s3');
        $tempFilePath = 'temp/'.$pendingFile->file_name;

        if (!Storage::exists($tempFilePath)) {
            abort(404);
        }

        if (!isset($disk) || $pendingFile->completed) {
            abort(403);
        }

        $pendingFile->update([
            'completed' => true
        ]);

        $mod = $pendingFile->mod;

        // Allow replacing the file despite having full storage
        $remainingStorage = $mod->currentStorage + ($pendingFile->file?->size ?? 0);

        try {
            $size = Storage::size($tempFilePath);

            if ($size !== $pendingFile->size || $size > $remainingStorage) {
                Storage::delete($tempFilePath);
                abort(403, 'File size is invalid or exceeds allowed storage!');
            }

            // For whatever reason this doesn't work with the normal Storage::move
            /** @var S3Client */
            $client = $disk->getClient();
            $client->copyObject([
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'CopySource' => config('filesystems.disks.s3.bucket').'/'.$tempFilePath,
                'Key' => 'mods/files/'.$pendingFile->file_name,
                'ACL' => 'public-read',
            ]);

            $disk->delete($tempFilePath);

            $file = null;
            $pendingFile->file->update([
                'completed' => true,
                'file' => $pendingFile->file_name,
                'type' => $pendingFile->file_type,
                'size' => $size
            ]);

            $file = $pendingFile->file;
            $file->refresh();

            // Done copying and moving the file + updating the related file
            // In case anything above fails, we'd handle it in DeleteLoosePendingFiles job
            $pendingFile->delete();

            $mod->refresh();
            $mod->bump(false);
            $mod->calculateFileStatus();

            Utils::sendWebhook('file_uploaded', [
                'file_id' => $file->id,
                'mod_id' => $file->mod_id,
                'user_id' => $this->userId(),
            ], ['link' => $file->downloadUrl], $file->mod->game_id);
            return $file;
        } catch (AwsException $e) {
            // Output error message if something goes wrong
            abort(500, "Error copying object: " . $e->getMessage());
        }
    }

    /**
     * Upload a mod file
     *
     * Upload a new file to a mod.
     *
     * @authenticated
     */
    public function store(FileRequest $request, Mod $mod)
    {
        set_time_limit(3600);

        $remainingStorage = $mod->currentStorage;
        $val = $request->val([
            'actual_file' => "nullable|file|max:{$remainingStorage}"
        ]);

        APIService::nullToEmptyStr($val, 'label', 'desc', 'version');

        $imageId = Arr::pull($val, 'image_id');
        if (isset($imageId) && !$mod->images()->where('id', $imageId)->exists()) {
            abort(422, 'Invalid image. Are you sure it exists and belongs to the mod?');
        }

        $actualFile = Arr::pull($val, 'actual_file');
        $hasFile = isset($actualFile);

        if ($hasFile) {
            [$uploadedFile, $name, $type] = ModService::attemptUpload($mod, $actualFile);
        }

        $file = $mod->files()->create([
            // This is not the actual name of the file stored in our storage
            'name' => isset($uploadedFile) ? explode('.', $uploadedFile->getClientOriginalName())[0] : explode('.', $val['name'])[0],
            'desc' => $val['desc'],
            'user_id' => $this->userId(),
            'file' => $name ?? '', // This is though
            'type' => $type ?? '',
            'size' => isset($uploadedFile) ? $uploadedFile->getSize() : 0,
            'completed' => isset($hasFile),
            'label' => $val['label'],
            'version' => $val['version'],
            'image_id' => $imageId,
        ]);

        $mod->refresh();
        if (isset($uploadedFile)) {
            $mod->bump(false);
        }
        $mod->calculateFileStatus(); //Here it saves

        if ($mod->files_are_versions && isset($val['version']) && $val['version'] !== $mod->version) {
            $followers = $mod->followers;
            foreach ($followers as $follow) {
                Notification::send(
                    notifiable: $mod,
                    user: $follow->user,
                    type: "follow_mod_new_version",
                    data: ['version' => $mod->version]
                );
            }
        }


        return response($file, 201);
    }

    /**
     * Get a file
     *
     * Returns a file. If the mod it belongs to is accessible.
     */
    public function show(File $file)
    {
        return $file;
    }

    /**
     * Update a file
     *
     * @authenticated
     */
    public function update(FileRequest $request, File $file)
    {
        set_time_limit(3600);
        // Since we are changing a file
        $remainingStorage = $file->mod->currentStorage + $file->size;

        $val = $request->val([
            'actual_file' => "nullable|file|max:{$remainingStorage}"
        ]);

        APIService::nullToEmptyStr($val, 'label', 'desc', 'version');

        $imageId = Arr::pull($val, 'image_id');
        if (isset($imageId) && !$file->mod->images()->where('id', $imageId)->exists()) {
            abort(422, 'Invalid image. Are you sure it exists and belongs to the mod?');
        }

        // Disabled for now: https://modworkshop.net/thread/13138
        // if ((isset($val['version']) && $val['version'] !== $file->version)) {
        //     $file->mod->bump();
        // }

        $actualFile = Arr::pull($val, 'actual_file');
        if (isset($actualFile)) {
            [$uploadFile, $name, $type] = ModService::attemptUpload($file->mod, $actualFile);

            //Delete old file
            Storage::delete('mods/files/'.$file->file);

            $val['file'] = $name;
            $val['type'] = $type;
            $val['size'] = $uploadFile->getSize();

            $file->mod->bump();
        }

        if (array_key_exists('display_order', $val)) {
            $val['display_order'] ??= 0;
        }

        $file->update($val);

        return new FileResource($file);
    }

    /**
     * Delete a file
     *
     * @authenticated
     */
    public function destroy(File $file)
    {
        $file->delete(); //Deletion of files handled in the model class.
    }

    /**
     * Download a file
     *
     * Begins a download of a file
     */
    public function downloadFile(File $file, Mod $mod=null)
    {
        return redirect($file->downloadUrl);
    }

    /**
     * Get file version
     *
     * Returns the version set for the file
     */
    public function fileVersion(File $file, Mod $mod=null)
    {
        return $file->version;
    }

    /**
     * Delete all mod files
     *
     * @authenticated
     */
    public function deleteAllFiles(Mod $mod)
    {
        foreach ($mod->files as $file) {
            $file->delete();
        }

        $mod->calculateFileStatus();
    }

    /**
     * List file versions
     *
     * Returns a list of versions (Up to 100 mods)
     * Convenient way of getting many versions at once and avoid sending too many requests
     * Warning: this bypasses any mod visibility/suspension, however the only information it returns are verisons, nothing else.
     * You cannot use this to download these files or figure out what mod they belongs to.
     */
    public function getVersions(Request $request) {
        $val = $request->validate([
            'file_ids' => 'array|required',
            'file_ids.*' => 'integer|min:1',
        ]);

        $files = File::whereIn('id', $val['file_ids'])->limit(100)->get();
        $onlyVersions = [];
        foreach($files as $file) {
            $onlyVersions[$file->id] = $file->version;
        }

        return $onlyVersions;
    }

    /**
     * Register download
     *
     * Registers a download for the file, doesn't let you 'download' it twice
     * It applies the download to the mod that the file belongs to.
     * Works with guests
     */
    public function registerDownload(Request $request, File $file)
    {
        return ModService::registerDownload($file);
    }

    /**
     * Get Latest File
     *
     * Returns the latest file (non prerelease by default). This works best with semver
     * If you wish to include prerelease, include ?prerelease=1 in the search query
     */
    public function getLatestFile(Request $request, Mod $mod) {
        $val = $request->validate([
            'prerelease' => 'boolean|nullable',
        ]);

        $files = $mod->files()->orderByRaw("semver_version IS NOT NULL DESC, display_order DESC, semver_version DESC, updated_at DESC");

        if (!isset($val['prerelease']) || !$val['prerelease']) {
            $files->whereRaw("(get_semver_prerelease (semver_version) = '') IS NOT FALSE");
        }
        return $files->first();
    }

    /**
     * Get Latest File Version
     *
     * Same as Get Latest File, but returns the version of the file
     */
    function getLatestFileVersion(Request $request, Mod $mod) {
        $file = $this->getLatestFile($request, $mod);
        if (!isset($file)) {
            abort(404);
        }
        return $file->version;
    }

    /**
     * Get Latest File Version
     *
     * Same as Get Latest File, but returns the version of the file
     */
    function downloadLatestFileVersion(Request $request, Mod $mod) {
        $file = $this->getLatestFile($request, $mod);
        if (!isset($file)) {
            abort(404);
        }
        return redirect($file->downloadUrl);
    }

    /**
     * Get Primary File
     *
     * Returns the primary file of the mod or the only file
     */
    public function getPrimaryFile(Request $request, Mod $mod) {
        $file = $mod->download_strictly_file;
        if (!isset($file)) {
            abort(404);
        }
        return $file;
    }

    /**
     * Get Primary File Version
     *
     * Same as Get Primary File, but returns the version of the file
     */
    function getPrimaryFileVersion(Request $request, Mod $mod) {
        $file = $this->getPrimaryFile($request, $mod);
        if (!isset($file)) {
            abort(404);
        }
        return $file->version;
    }

    /**
     * Get File by Version
     *
     * Gets a file using a specific version (works only with semver)
     */
    public function getFileByVersion(Mod $mod, string $version) {
        if (Version::parseOrNull($version) == null) {
            abort(422, 'Invalid semver');
        }

        $file = $mod->files()->whereNotNull('semver_version')
            ->where('semver_version', '=', $version)
            ->first();
        return $file;
    }
}
