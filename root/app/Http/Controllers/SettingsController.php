<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\APIService;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingsController extends Controller
{
    public function __construct() {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return APIService::getSettings();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request)
    {
        $val = $request->validate([
            'max_file_size' => 'integer',
            'mod_storage_size' => 'integer',
            'image_max_file_size' => 'integer',
            'mod_max_image_count' => 'integer',
            'news_forum_category' => 'integer',
            'game_requests_forum_category' => 'integer',
            'discord_webhook' => 'string|nullable|max:255',
            'discord_suspension_webhook' => 'string|nullable|max:255',
            'discord_approval_webhook' => 'string|nullable|max:255',
        ]);

        APIService::nullToEmptyStr($val,
            'discord_webhook',
            'discord_approval_webhook',
            'discord_suspension_webhook',
        );

        foreach ($val as $key => $value) {
            Setting::where('name', $key)->where('value', '!=', $value)->update(['value' => $value]);
        }
    }
}
