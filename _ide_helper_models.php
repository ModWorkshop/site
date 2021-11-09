<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 * @property string $desc
 * @property bool $hidden
 * @property bool $grid
 * @property int $disporder
 * @property int|null $parent_id
 * @property int|null $game_id
 * @property string $thumbnail
 * @property string $banner
 * @property string $buttons
 * @property string $webhook_url
 * @property bool $approval_only
 * @property string $last_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Category|null $game
 * @property-read mixed $path
 * @property-read Category|null $parent
 * @method static \Database\Factories\CategoryFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereApprovalOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereButtons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDisporder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereGrid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereLastDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereWebhookUrl($value)
 * @mixin \Eloquent
 * @property-read mixed $breadcrumb
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Comment
 *
 * @property int $id
 * @property string $commentable_type
 * @property int $commentable_id
 * @property int $user_id
 * @property string $content
 * @property bool $pinned
 * @property int|null $reply_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $commentable
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $lastReplies
 * @property-read int|null $last_replies_count
 * @property-read Comment $replyingComment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment wherePinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereReplyTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserId($value)
 * @mixin \Eloquent
 */
	class Comment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\File
 *
 * @property int $id
 * @property int $user_id
 * @property int $mod_id
 * @property string $name
 * @property string $desc
 * @property string $file
 * @property string $type
 * @property int|null $image_id
 * @property int $size
 * @property bool $approved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Mod $mod
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Image
 *
 * @property int $id
 * @property int $user_id
 * @property bool $has_thumb
 * @property string $file
 * @property string $type
 * @property int $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereHasThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUserId($value)
 * @mixin \Eloquent
 * @property int $mod_id
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereModId($value)
 */
	class Image extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Mod
 *
 * @property int $id
 * @property int|null $thumbnail_id
 * @property int|null $category_id
 * @property int|null $game_id
 * @property int $submitter_id
 * @property string $name
 * @property string $desc
 * @property string $short_desc
 * @property string $changelog
 * @property string $license
 * @property string $instructions
 * @property mixed|null $depends_on
 * @property int $visibility
 * @property string $banner
 * @property string $url
 * @property int $downloads
 * @property int $views
 * @property string $version
 * @property string $donation
 * @property string $access_ids
 * @property bool $suspended
 * @property bool $comments_disabled
 * @property int $file_status
 * @property float $score
 * @property string|null $bump_date
 * @property string|null $publish_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Category|null $game
 * @property-read \App\Models\User|null $submitter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\ModFactory factory(...$parameters)
 * @method static Builder|Mod list()
 * @method static Builder|Mod newModelQuery()
 * @method static Builder|Mod newQuery()
 * @method static Builder|Mod query()
 * @method static Builder|Mod whereAccessIds($value)
 * @method static Builder|Mod whereBanner($value)
 * @method static Builder|Mod whereBumpDate($value)
 * @method static Builder|Mod whereCategoryId($value)
 * @method static Builder|Mod whereChangelog($value)
 * @method static Builder|Mod whereCommentsDisabled($value)
 * @method static Builder|Mod whereCreatedAt($value)
 * @method static Builder|Mod whereDependsOn($value)
 * @method static Builder|Mod whereDesc($value)
 * @method static Builder|Mod whereDonation($value)
 * @method static Builder|Mod whereDownloads($value)
 * @method static Builder|Mod whereFileStatus($value)
 * @method static Builder|Mod whereGameId($value)
 * @method static Builder|Mod whereId($value)
 * @method static Builder|Mod whereInstructions($value)
 * @method static Builder|Mod whereLicense($value)
 * @method static Builder|Mod whereName($value)
 * @method static Builder|Mod wherePublishDate($value)
 * @method static Builder|Mod whereScore($value)
 * @method static Builder|Mod whereShortDesc($value)
 * @method static Builder|Mod whereSubmitterUid($value)
 * @method static Builder|Mod whereSuspended($value)
 * @method static Builder|Mod whereThumbnailId($value)
 * @method static Builder|Mod whereUpdatedAt($value)
 * @method static Builder|Mod whereUrl($value)
 * @method static Builder|Mod whereVersion($value)
 * @method static Builder|Mod whereViews($value)
 * @method static Builder|Mod whereVisibility($value)
 * @mixin \Eloquent
 * @property-read void $breadcrumb
 * @property-read mixed $tag_ids
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 * @property int|null $download_id
 * @property string|null $download_type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read int|null $files_count
 * @method static Builder|Mod whereDownloadId($value)
 * @method static Builder|Mod whereDownloadType($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read Model|\Eloquent $download
 * @method static Builder|Mod whereSubmitterId($value)
 */
	class Mod extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $desc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PermissionsRolesLink
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionsRolesLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionsRolesLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionsRolesLink query()
 * @mixin \Eloquent
 */
	class PermissionsRolesLink extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string $tag
 * @property string $desc
 * @property string $color
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SocialLogin
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $social_id
 * @property string $special_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereSpecialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereUserId($value)
 */
	class SocialLogin extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property string $notice
 * @property int $notice_type
 * @property bool $notice_localized
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\TagFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereNoticeLocalized($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereNoticeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $avatar
 * @property-read mixed $permissions
 * @property-read mixed $role_names
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAvatar($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User withPermissions()
 * @mixin \Eloquent
 * @property-read \App\Models\UserExtra|null $extra
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserExtra
 *
 * @property int $id
 * @property string $banner
 * @property string $bio
 * @property bool $private_profile
 * @property string $custom_title
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereCustomTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereLastOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra wherePrivateProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereLastOnlineAt($value)
 * @property Carbon|null $last_online
 */
	class UserExtra extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UsersRolesLink
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRolesLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRolesLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRolesLink query()
 * @mixin \Eloquent
 */
	class UsersRolesLink extends \Eloquent {}
}

