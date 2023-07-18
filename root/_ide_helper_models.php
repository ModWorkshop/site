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
 * App\Models\Ban
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $reason
 * @property Carbon $expire_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @method static Builder|Ban newModelQuery()
 * @method static Builder|Ban newQuery()
 * @method static Builder|Ban query()
 * @method static Builder|Ban whereCreatedAt($value)
 * @method static Builder|Ban whereExpireDate($value)
 * @method static Builder|Ban whereId($value)
 * @method static Builder|Ban whereReason($value)
 * @method static Builder|Ban whereUpdatedAt($value)
 * @method static Builder|Ban whereUserId($value)
 * @property int|null $case_id
 * @property-read UserCase|null $case
 * @method static Builder|Ban whereCaseId($value)
 * @property int|null $game_id
 * @method static Builder|Ban whereGameId($value)
 * @property bool $can_appeal
 * @method static Builder|Ban whereCanAppeal($value)
 * @property int|null $mod_user_id
 * @property bool $active
 * @property-read User|null $modUser
 * @method static Builder|Ban whereActive($value)
 * @method static Builder|Ban whereModUserId($value)
 * @mixin Eloquent
 */
	class Ban extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BlockedTag
 *
 * @property int $id
 * @property int $user_id
 * @property int $tag_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|BlockedTag newModelQuery()
 * @method static Builder|BlockedTag newQuery()
 * @method static Builder|BlockedTag query()
 * @method static Builder|BlockedTag whereCreatedAt($value)
 * @method static Builder|BlockedTag whereId($value)
 * @method static Builder|BlockedTag whereTagId($value)
 * @method static Builder|BlockedTag whereUpdatedAt($value)
 * @method static Builder|BlockedTag whereUserId($value)
 * @mixin Eloquent
 */
	class BlockedTag extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BlockedUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $block_user_id
 * @property bool $silent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|BlockedUser newModelQuery()
 * @method static Builder|BlockedUser newQuery()
 * @method static Builder|BlockedUser query()
 * @method static Builder|BlockedUser whereBlockUserId($value)
 * @method static Builder|BlockedUser whereCreatedAt($value)
 * @method static Builder|BlockedUser whereId($value)
 * @method static Builder|BlockedUser whereSilent($value)
 * @method static Builder|BlockedUser whereUpdatedAt($value)
 * @method static Builder|BlockedUser whereUserId($value)
 * @mixin Eloquent
 */
	class BlockedUser extends \Eloquent {}
}

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
 * @method static CategoryFactory factory(...$parameters)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereApprovalOnly($value)
 * @method static Builder|Category whereBanner($value)
 * @method static Builder|Category whereButtons($value)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereDesc($value)
 * @method static Builder|Category whereDisporder($value)
 * @method static Builder|Category whereGameId($value)
 * @method static Builder|Category whereGrid($value)
 * @method static Builder|Category whereHidden($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereLastDate($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereParentId($value)
 * @method static Builder|Category whereShortName($value)
 * @method static Builder|Category whereThumbnail($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @method static Builder|Category whereWebhookUrl($value)
 * @property-read mixed $breadcrumb
 * @mixin Eloquent
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $commentable
 * @property-read Collection|Comment[] $lastReplies
 * @property-read int|null $last_replies_count
 * @property-read Comment $replyingComment
 * @property-read User $user
 * @method static Builder|Comment newModelQuery()
 * @method static Builder|Comment newQuery()
 * @method static Builder|Comment query()
 * @method static Builder|Comment whereCommentableId($value)
 * @method static Builder|Comment whereCommentableType($value)
 * @method static Builder|Comment whereContent($value)
 * @method static Builder|Comment whereCreatedAt($value)
 * @method static Builder|Comment whereId($value)
 * @method static Builder|Comment wherePinned($value)
 * @method static Builder|Comment whereReplyTo($value)
 * @method static Builder|Comment whereUpdatedAt($value)
 * @method static Builder|Comment whereUserId($value)
 * @property-read Collection|User[] $mentions
 * @property-read int|null $mentions_count
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection|Comment[] $replies
 * @property-read int|null $replies_count
 * @property-read Subscription|null $subscribed
 * @property-read Collection|Report[] $reports
 * @property-read int|null $reports_count
 * @mixin Eloquent
 */
	class Comment extends \Eloquent implements \App\Interfaces\SubscribableInterface {}
}

namespace App\Models{
/**
 * App\Models\Dependency
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $url
 * @property bool $offsite
 * @property int|null $mod_id
 * @property bool $optional
 * @property string $dependable_type
 * @property int $dependable_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Mod|null $mod
 * @method static Builder|Dependency newModelQuery()
 * @method static Builder|Dependency newQuery()
 * @method static Builder|Dependency query()
 * @method static Builder|Dependency whereCreatedAt($value)
 * @method static Builder|Dependency whereDependableId($value)
 * @method static Builder|Dependency whereDependableType($value)
 * @method static Builder|Dependency whereId($value)
 * @method static Builder|Dependency whereModId($value)
 * @method static Builder|Dependency whereName($value)
 * @method static Builder|Dependency whereOffsite($value)
 * @method static Builder|Dependency whereOptional($value)
 * @method static Builder|Dependency whereOrder($value)
 * @method static Builder|Dependency whereUpdatedAt($value)
 * @method static Builder|Dependency whereUrl($value)
 * @property-read Model|Eloquent $dependable
 * @mixin Eloquent
 */
	class Dependency extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Document
 *
 * @property int $id
 * @property string $name
 * @property string $url_name
 * @property string $desc
 * @property int $game_id
 * @property int $last_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Document newModelQuery()
 * @method static Builder|Document newQuery()
 * @method static Builder|Document query()
 * @method static Builder|Document whereCreatedAt($value)
 * @method static Builder|Document whereDesc($value)
 * @method static Builder|Document whereGameId($value)
 * @method static Builder|Document whereId($value)
 * @method static Builder|Document whereLastUserId($value)
 * @method static Builder|Document whereName($value)
 * @method static Builder|Document whereUpdatedAt($value)
 * @method static Builder|Document whereUrlName($value)
 * @property-read User $lastUser
 * @property-read Game|null $game
 * @mixin Eloquent
 */
	class Document extends \Eloquent {}
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|File newModelQuery()
 * @method static Builder|File newQuery()
 * @method static Builder|File query()
 * @method static Builder|File whereApproved($value)
 * @method static Builder|File whereCreatedAt($value)
 * @method static Builder|File whereDesc($value)
 * @method static Builder|File whereFile($value)
 * @method static Builder|File whereId($value)
 * @method static Builder|File whereImageId($value)
 * @method static Builder|File whereModId($value)
 * @method static Builder|File whereName($value)
 * @method static Builder|File whereSize($value)
 * @method static Builder|File whereType($value)
 * @method static Builder|File whereUpdatedAt($value)
 * @method static Builder|File whereUserId($value)
 * @property-read Mod $mod
 * @property string $label
 * @property string $version
 * @property-read User $user
 * @method static Builder|File whereLabel($value)
 * @method static Builder|File whereVersion($value)
 * @property string|null $unique_name
 * @property-read Image|null $image
 * @method static Builder|File whereUniqueName($value)
 * @mixin Eloquent
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FollowedGame
 *
 * @property int $id
 * @property int $user_id
 * @property int $game_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|FollowedGame newModelQuery()
 * @method static Builder|FollowedGame newQuery()
 * @method static Builder|FollowedGame query()
 * @method static Builder|FollowedGame whereCreatedAt($value)
 * @method static Builder|FollowedGame whereGameId($value)
 * @method static Builder|FollowedGame whereId($value)
 * @method static Builder|FollowedGame whereUpdatedAt($value)
 * @method static Builder|FollowedGame whereUserId($value)
 * @property-read User $user
 * @mixin Eloquent
 */
	class FollowedGame extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FollowedMod
 *
 * @property int $id
 * @property int $user_id
 * @property int $mod_id
 * @property bool $notify
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|FollowedMod newModelQuery()
 * @method static Builder|FollowedMod newQuery()
 * @method static Builder|FollowedMod query()
 * @method static Builder|FollowedMod whereCreatedAt($value)
 * @method static Builder|FollowedMod whereId($value)
 * @method static Builder|FollowedMod whereModId($value)
 * @method static Builder|FollowedMod whereNotify($value)
 * @method static Builder|FollowedMod whereUpdatedAt($value)
 * @method static Builder|FollowedMod whereUserId($value)
 * @property-read User $user
 * @mixin Eloquent
 */
	class FollowedMod extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FollowedUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $follow_user_id
 * @property bool $notify
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|FollowedUser newModelQuery()
 * @method static Builder|FollowedUser newQuery()
 * @method static Builder|FollowedUser query()
 * @method static Builder|FollowedUser whereCreatedAt($value)
 * @method static Builder|FollowedUser whereFollowUserId($value)
 * @method static Builder|FollowedUser whereId($value)
 * @method static Builder|FollowedUser whereNotify($value)
 * @method static Builder|FollowedUser whereUpdatedAt($value)
 * @method static Builder|FollowedUser whereUserId($value)
 * @property-read User $user
 * @mixin Eloquent
 */
	class FollowedUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Forum
 *
 * @property int $id
 * @property int|null $game_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Forum newModelQuery()
 * @method static Builder|Forum newQuery()
 * @method static Builder|Forum query()
 * @method static Builder|Forum whereCreatedAt($value)
 * @method static Builder|Forum whereGameId($value)
 * @method static Builder|Forum whereId($value)
 * @method static Builder|Forum whereUpdatedAt($value)
 * @property-read Game|null $game
 * @property-read Collection|Thread[] $threads
 * @property-read int|null $threads_count
 * @property string $name
 * @property-read Collection|ForumCategory[] $categories
 * @property-read int|null $categories_count
 * @method static Builder|Forum whereName($value)
 * @mixin Eloquent
 */
	class Forum extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ForumCategory
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $forum_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ForumCategory newModelQuery()
 * @method static Builder|ForumCategory newQuery()
 * @method static Builder|ForumCategory query()
 * @method static Builder|ForumCategory whereCreatedAt($value)
 * @method static Builder|ForumCategory whereDesc($value)
 * @method static Builder|ForumCategory whereForumId($value)
 * @method static Builder|ForumCategory whereId($value)
 * @method static Builder|ForumCategory whereName($value)
 * @method static Builder|ForumCategory whereUpdatedAt($value)
 * @property string $emoji
 * @property-read Forum $forum
 * @method static Builder|ForumCategory whereEmoji($value)
 * @property bool $is_private
 * @property bool $private_threads
 * @property bool $banned_can_post
 * @property string|null $announce_until
 * @property-read Collection|GameRole[] $gameRoles
 * @property-read int|null $game_roles_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @method static Builder|ForumCategory whereAnnounceUntil($value)
 * @method static Builder|ForumCategory whereBannedCanPost($value)
 * @method static Builder|ForumCategory whereIsPrivate($value)
 * @method static Builder|ForumCategory wherePrivateThreads($value)
 * @mixin Eloquent
 */
	class ForumCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Game
 *
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 * @property int $disporder
 * @property string $thumbnail
 * @property string $banner
 * @property string $buttons
 * @property string $webhook_url
 * @property string $last_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $breadcrumb
 * @method static Builder|Game newModelQuery()
 * @method static Builder|Game newQuery()
 * @method static Builder|Game query()
 * @method static Builder|Game whereBanner($value)
 * @method static Builder|Game whereButtons($value)
 * @method static Builder|Game whereCreatedAt($value)
 * @method static Builder|Game whereDisporder($value)
 * @method static Builder|Game whereId($value)
 * @method static Builder|Game whereLastDate($value)
 * @method static Builder|Game whereName($value)
 * @method static Builder|Game whereShortName($value)
 * @method static Builder|Game whereThumbnail($value)
 * @method static Builder|Game whereUpdatedAt($value)
 * @method static Builder|Game whereWebhookUrl($value)
 * @property-read Forum|null $forum
 * @property int|null $forum_id
 * @method static Builder|Game whereForumId($value)
 * @property-read FollowedGame|null $followed
 * @property-read Collection|Mod[] $mods
 * @property-read int|null $mod_count
 * @property-read Collection|GameRole[] $roles
 * @property-read int|null $roles_count
 * @property-read mixed $user_data
 * @method static Builder|Game whereModsCount($value)
 * @property-read int|null $mods_count
 * @property-read Collection<int, Report> $reports
 * @property-read int|null $reports_count
 * @method static Builder|Game whereModCount($value)
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read Collection<int, Report> $reports
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $reports
 */
	class Game extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GameRole
 *
 * @property int $id
 * @property string $name
 * @property string $tag
 * @property string $desc
 * @property string $color
 * @property int $game_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $is_vanity
 * @property-read Game $game
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static Builder|GameRole newModelQuery()
 * @method static Builder|GameRole newQuery()
 * @method static Builder|GameRole query()
 * @method static Builder|GameRole whereColor($value)
 * @method static Builder|GameRole whereCreatedAt($value)
 * @method static Builder|GameRole whereDesc($value)
 * @method static Builder|GameRole whereGameId($value)
 * @method static Builder|GameRole whereId($value)
 * @method static Builder|GameRole whereIsVanity($value)
 * @method static Builder|GameRole whereName($value)
 * @method static Builder|GameRole whereOrder($value)
 * @method static Builder|GameRole whereTag($value)
 * @method static Builder|GameRole whereUpdatedAt($value)
 * @property bool $self_assignable
 * @method static Builder|GameRole whereSelfAssignable($value)
 * @mixin Eloquent
 */
	class GameRole extends \Eloquent {}
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Image newModelQuery()
 * @method static Builder|Image newQuery()
 * @method static Builder|Image query()
 * @method static Builder|Image whereCreatedAt($value)
 * @method static Builder|Image whereFile($value)
 * @method static Builder|Image whereHasThumb($value)
 * @method static Builder|Image whereId($value)
 * @method static Builder|Image whereSize($value)
 * @method static Builder|Image whereType($value)
 * @method static Builder|Image whereUpdatedAt($value)
 * @method static Builder|Image whereUserId($value)
 * @property int $mod_id
 * @method static Builder|Image whereModId($value)
 * @property-read Mod $mod
 * @mixin Eloquent
 */
	class Image extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\InstructsTemplate
 *
 * @property int $id
 * @property string $name
 * @property string $instructions
 * @property bool $localized
 * @property int $game_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Dependency[] $dependencies
 * @property-read int|null $dependencies_count
 * @method static Builder|InstructsTemplate newModelQuery()
 * @method static Builder|InstructsTemplate newQuery()
 * @method static Builder|InstructsTemplate query()
 * @method static Builder|InstructsTemplate whereCreatedAt($value)
 * @method static Builder|InstructsTemplate whereGameId($value)
 * @method static Builder|InstructsTemplate whereId($value)
 * @method static Builder|InstructsTemplate whereInstructions($value)
 * @method static Builder|InstructsTemplate whereLocalized($value)
 * @method static Builder|InstructsTemplate whereName($value)
 * @method static Builder|InstructsTemplate whereUpdatedAt($value)
 * @property-read Game $game
 * @mixin Eloquent
 */
	class InstructsTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Link
 *
 * @property int $id
 * @property int $user_id
 * @property int $mod_id
 * @property string $name
 * @property string $desc
 * @property string $label
 * @property string $url
 * @property string $version
 * @property int|null $image_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Mod $mod
 * @property-read User $user
 * @method static Builder|Link newModelQuery()
 * @method static Builder|Link newQuery()
 * @method static Builder|Link query()
 * @method static Builder|Link whereCreatedAt($value)
 * @method static Builder|Link whereDesc($value)
 * @method static Builder|Link whereId($value)
 * @method static Builder|Link whereImageId($value)
 * @method static Builder|Link whereLabel($value)
 * @method static Builder|Link whereModId($value)
 * @method static Builder|Link whereName($value)
 * @method static Builder|Link whereUpdatedAt($value)
 * @method static Builder|Link whereUrl($value)
 * @method static Builder|Link whereUserId($value)
 * @method static Builder|Link whereVersion($value)
 * @mixin Eloquent
 */
	class Link extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Mention
 *
 * @property int $id
 * @property int $user_id
 * @property int $comment_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Mention newModelQuery()
 * @method static Builder|Mention newQuery()
 * @method static Builder|Mention query()
 * @method static Builder|Mention whereCommentId($value)
 * @method static Builder|Mention whereCreatedAt($value)
 * @method static Builder|Mention whereId($value)
 * @method static Builder|Mention whereUpdatedAt($value)
 * @method static Builder|Mention whereUserId($value)
 * @mixin Eloquent
 */
	class Mention extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Mod
 *
 * @property int $id
 * @property int|null $thumbnail_id
 * @property int|null $category_id
 * @property int|null $game_id
 * @property int $user_id
 * @property string $name
 * @property string $desc
 * @property string $short_desc
 * @property string $changelog
 * @property string $license
 * @property string $instructions
 * @property mixed|null $depends_on
 * @property int $visibility
 * @property string $legacy_banner_url
 * @property string $url
 * @property int $downloads
 * @property int $views
 * @property int $likes
 * @property string $version
 * @property string $donation
 * @property bool $suspended
 * @property bool $comments_disabled
 * @property int $file_status
 * @property float $score
 * @property string|null $bumped_at
 * @property string|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Category|null $category
 * @property-read Game|null $game
 * @property-read User|null $user
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @method static ModFactory factory(...$parameters)
 * @method static Builder|Mod list()
 * @method static Builder|Mod newModelQuery()
 * @method static Builder|Mod newQuery()
 * @method static Builder|Mod query()
 * @method static Builder|Mod whereAccessIds($value)
 * @method static Builder|Mod whereLegacyBannerUrl($value)
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
 * @method static Builder|Mod whereUserUid($value)
 * @method static Builder|Mod whereSuspended($value)
 * @method static Builder|Mod whereThumbnailId($value)
 * @method static Builder|Mod whereUpdatedAt($value)
 * @method static Builder|Mod whereUrl($value)
 * @method static Builder|Mod whereVersion($value)
 * @method static Builder|Mod whereViews($value)
 * @method static Builder|Mod whereVisibility($value)
 * @property-read void $breadcrumb
 * @property-read mixed $tag_ids
 * @property-read Collection|Image[] $images
 * @property-read int|null $images_count
 * @property int|null $
 * _id
 * @property string|null $download_type
 * @property-read Collection|File[] $files
 * @property-read int|null $files_count
 * @method static Builder|Mod whereDownloadId($value)
 * @method static Builder|Mod whereDownloadType($value)
 * @property-read Collection|Comment[] $comments
 * @property-read int|null $comment_count
 * @property-read Model|Eloquent $download
 * @method static Builder|Mod whereUserId($value)
 * @property-read Image|null $thumbnail
 * @property int|null $banner_id
 * @property-read Image|null $banner
 * @property-read bool $liked
 * @property-read Collection|Link[] $links
 * @property-read int|null $links_count
 * @property-read Collection|ModMember[] $members
 * @property-read int|null $members_count
 * @method static Builder|Mod whereBannerId($value)
 * @method static Builder|Mod whereLikes($value)
 * @method static Builder|Mod whereBumpedAt($value)
 * @method static Builder|Mod wherePublishedAt($value)
 * @property-read TransferRequest|null $transferRequest
 * @property int|null $last_user_id
 * @property-read User|null $lastUser
 * @method static Builder|Mod whereLastUserId($value)
 * @property string $access_ids
 * @property-read Follows|null $follows
 * @property-read Follows|null $followsUsingCategory
 * @property-read Suspension|null $lastSuspension
 * @property-read FollowedMod|null $followed
 * @property-read Collection|Taggable[] $tagsSpecial
 * @property-read int|null $tags_special_count
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection|FollowedMod[] $followers
 * @property-read int|null $followers_count
 * @property-read Subscription|null $subscribed
 * @property bool $has_download
 * @property bool $approved
 * @method static Builder|Mod whereApproved($value)
 * @method static Builder|Mod whereHasDownload($value)
 * @property int|null $instructs_template_id
 * @property-read Collection|Dependency[] $dependencies
 * @property-read int|null $dependencies_count
 * @property-read InstructsTemplate|null $instructsTemplate
 * @property-read Collection|User[] $membersThatCanEdit
 * @property-read int|null $members_that_can_edit_count
 * @property-read Collection|Report[] $reports
 * @property-read int|null $reports_count
 * @method static Builder|Mod whereInstructsTemplateId($value)
 * @property float $daily_score
 * @property float $weekly_score
 * @property int|null $allowed_storage
 * @method static Builder|Mod whereAllowedStorage($value)
 * @method static Builder|Mod whereDailyScore($value)
 * @method static Builder|Mod whereWeeklyScore($value)
 * @property int|null $download_id
 * @property-read BlockedUser|null $blockedByMe
 * @property-read int|null $comments_count
 * @mixin Eloquent
 */
	class Mod extends \Eloquent implements \App\Interfaces\SubscribableInterface {}
}

namespace App\Models{
/**
 * App\Models\ModDownload
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property string $ip_address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ModDownload newModelQuery()
 * @method static Builder|ModDownload newQuery()
 * @method static Builder|ModDownload query()
 * @method static Builder|ModDownload whereCreatedAt($value)
 * @method static Builder|ModDownload whereId($value)
 * @method static Builder|ModDownload whereIpAddress($value)
 * @method static Builder|ModDownload whereModId($value)
 * @method static Builder|ModDownload whereUpdatedAt($value)
 * @method static Builder|ModDownload whereUserId($value)
 * @mixin Eloquent
 */
	class ModDownload extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ModLike
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ModLike newModelQuery()
 * @method static Builder|ModLike newQuery()
 * @method static Builder|ModLike query()
 * @method static Builder|ModLike whereCreatedAt($value)
 * @method static Builder|ModLike whereId($value)
 * @method static Builder|ModLike whereModId($value)
 * @method static Builder|ModLike whereUpdatedAt($value)
 * @method static Builder|ModLike whereUserId($value)
 * @mixin Eloquent
 */
	class ModLike extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ModMember
 *
 * @property int $id
 * @property int $level
 * @property int $mod_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ModMember newModelQuery()
 * @method static Builder|ModMember newQuery()
 * @method static Builder|ModMember query()
 * @method static Builder|ModMember whereCreatedAt($value)
 * @method static Builder|ModMember whereId($value)
 * @method static Builder|ModMember whereLevel($value)
 * @method static Builder|ModMember whereModId($value)
 * @method static Builder|ModMember whereUpdatedAt($value)
 * @method static Builder|ModMember whereUserId($value)
 * @property bool $accepted
 * @method static Builder|ModMember whereAccepted($value)
 * @property-read User|null $user
 * @mixin Eloquent
 */
	class ModMember extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ModView
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property string $ip_address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ModView newModelQuery()
 * @method static Builder|ModView newQuery()
 * @method static Builder|ModView query()
 * @method static Builder|ModView whereCreatedAt($value)
 * @method static Builder|ModView whereId($value)
 * @method static Builder|ModView whereIpAddress($value)
 * @method static Builder|ModView whereModId($value)
 * @method static Builder|ModView whereUpdatedAt($value)
 * @method static Builder|ModView whereUserId($value)
 * @mixin Eloquent
 */
	class ModView extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Notification
 *
 * @property int $id
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property string $context_type
 * @property int $context_id
 * @property string $type
 * @property bool $seen
 * @property mixed $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Notification newModelQuery()
 * @method static Builder|Notification newQuery()
 * @method static Builder|Notification query()
 * @method static Builder|Notification whereContextId($value)
 * @method static Builder|Notification whereContextType($value)
 * @method static Builder|Notification whereCreatedAt($value)
 * @method static Builder|Notification whereData($value)
 * @method static Builder|Notification whereId($value)
 * @method static Builder|Notification whereNotifiableId($value)
 * @method static Builder|Notification whereNotifiableType($value)
 * @method static Builder|Notification whereSeen($value)
 * @method static Builder|Notification whereType($value)
 * @method static Builder|Notification whereUpdatedAt($value)
 * @property-read Model|Eloquent $context
 * @property-read Model|Eloquent $notifiable
 * @property-read User $user
 * @property int $user_id
 * @method static Builder|Notification whereUserId($value)
 * @property int|null $from_user_id
 * @property-read User|null $fromUser
 * @method static Builder|Notification whereFromUserId($value)
 * @mixin Eloquent
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $desc
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|Permission query()
 * @method static Builder|Permission whereCreatedAt($value)
 * @method static Builder|Permission whereDesc($value)
 * @method static Builder|Permission whereId($value)
 * @method static Builder|Permission whereName($value)
 * @method static Builder|Permission whereSlug($value)
 * @method static Builder|Permission whereUpdatedAt($value)
 * @property string|null $type
 * @method static Builder|Permission whereType($value)
 * @mixin Eloquent
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PopularityLog
 *
 * @property int $id
 * @property string $type
 * @property int $mod_id
 * @property int $user_id
 * @property string $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static Builder|PopularityLog newModelQuery()
 * @method static Builder|PopularityLog newQuery()
 * @method static Builder|PopularityLog query()
 * @method static Builder|PopularityLog whereCreatedAt($value)
 * @method static Builder|PopularityLog whereId($value)
 * @method static Builder|PopularityLog whereIpAddress($value)
 * @method static Builder|PopularityLog whereModId($value)
 * @method static Builder|PopularityLog whereType($value)
 * @method static Builder|PopularityLog whereUserId($value)
 * @property-read Mod $mod
 * @property-read User $user
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|PopularityLog whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class PopularityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Report
 *
 * @property int $id
 * @property mixed $data
 * @property int $user_id
 * @property string $reason
 * @property bool $archived
 * @property string $reportable_type
 * @property int $reportable_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Report newModelQuery()
 * @method static Builder|Report newQuery()
 * @method static Builder|Report query()
 * @method static Builder|Report whereArchived($value)
 * @method static Builder|Report whereCreatedAt($value)
 * @method static Builder|Report whereData($value)
 * @method static Builder|Report whereId($value)
 * @method static Builder|Report whereReason($value)
 * @method static Builder|Report whereReportableId($value)
 * @method static Builder|Report whereReportableType($value)
 * @method static Builder|Report whereUpdatedAt($value)
 * @method static Builder|Report whereUserId($value)
 * @property string|null $name
 * @property int|null $game_id
 * @property-read Model|Eloquent $reportable
 * @property-read User $user
 * @method static Builder|Report whereGameId($value)
 * @method static Builder|Report whereName($value)
 * @property bool $locked
 * @method static Builder|Report whereLocked($value)
 * @property-read Game|null $game
 * @mixin Eloquent
 */
	class Report extends \Eloquent {}
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereColor($value)
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereDesc($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereOrder($value)
 * @method static Builder|Role whereTag($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @property bool $is_vanity
 * @method static Builder|Role whereIsVanity($value)
 * @property bool $self_assignable
 * @method static Builder|Role whereSelfAssignable($value)
 * @mixin Eloquent
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RoleUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|RoleUser newModelQuery()
 * @method static Builder|RoleUser newQuery()
 * @method static Builder|RoleUser query()
 * @method static Builder|RoleUser whereCreatedAt($value)
 * @method static Builder|RoleUser whereId($value)
 * @method static Builder|RoleUser whereRoleId($value)
 * @method static Builder|RoleUser whereUpdatedAt($value)
 * @method static Builder|RoleUser whereUserId($value)
 * @mixin Eloquent
 */
	class RoleUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $value
 * @property bool $public
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereCreatedAt($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereName($value)
 * @method static Builder|Setting wherePublic($value)
 * @method static Builder|Setting whereType($value)
 * @method static Builder|Setting whereUpdatedAt($value)
 * @method static Builder|Setting whereValue($value)
 * @mixin Eloquent
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SocialLogin
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $social_id
 * @property string $special_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @method static Builder|SocialLogin newModelQuery()
 * @method static Builder|SocialLogin newQuery()
 * @method static Builder|SocialLogin query()
 * @method static Builder|SocialLogin whereCreatedAt($value)
 * @method static Builder|SocialLogin whereId($value)
 * @method static Builder|SocialLogin whereSocialId($value)
 * @method static Builder|SocialLogin whereSpecialId($value)
 * @method static Builder|SocialLogin whereUpdatedAt($value)
 * @method static Builder|SocialLogin whereUserId($value)
 * @mixin Eloquent
 */
	class SocialLogin extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Subscription
 *
 * @property int $id
 * @property int $user_id
 * @property string $subscribable_type
 * @property int $subscribable_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Subscription newModelQuery()
 * @method static Builder|Subscription newQuery()
 * @method static Builder|Subscription query()
 * @method static Builder|Subscription whereCreatedAt($value)
 * @method static Builder|Subscription whereId($value)
 * @method static Builder|Subscription whereSubscribableId($value)
 * @method static Builder|Subscription whereSubscribableType($value)
 * @method static Builder|Subscription whereUpdatedAt($value)
 * @method static Builder|Subscription whereUserId($value)
 * @property-read User $user
 * @mixin Eloquent
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Supporter
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $expire_date
 * @property bool $expired
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|Supporter newModelQuery()
 * @method static Builder|Supporter newQuery()
 * @method static Builder|Supporter query()
 * @method static Builder|Supporter whereCreatedAt($value)
 * @method static Builder|Supporter whereExpireDate($value)
 * @method static Builder|Supporter whereId($value)
 * @method static Builder|Supporter whereIsCancelled($value)
 * @method static Builder|Supporter whereUpdatedAt($value)
 * @method static Builder|Supporter whereUserId($value)
 * @method static Builder|Supporter whereExpired($value)
 * @mixin Eloquent
 */
	class Supporter extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Suspension
 *
 * @property int $id
 * @property int $mod_id
 * @property int|null $mod_user_id
 * @property string $reason
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Suspension newModelQuery()
 * @method static Builder|Suspension newQuery()
 * @method static Builder|Suspension query()
 * @method static Builder|Suspension whereCreatedAt($value)
 * @method static Builder|Suspension whereId($value)
 * @method static Builder|Suspension whereModId($value)
 * @method static Builder|Suspension whereModUserId($value)
 * @method static Builder|Suspension whereReason($value)
 * @method static Builder|Suspension whereStatus($value)
 * @method static Builder|Suspension whereUpdatedAt($value)
 * @property-read Mod $mod
 * @mixin Eloquent
 */
	class Suspension extends \Eloquent {}
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static TagFactory factory(...$parameters)
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag query()
 * @method static Builder|Tag whereColor($value)
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereNotice($value)
 * @method static Builder|Tag whereNoticeLocalized($value)
 * @method static Builder|Tag whereNoticeType($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 * @property-read Collection|Category[] $categories
 * @property-read int|null $categories_count
 * @property-read Collection|Game[] $games
 * @property-read int|null $games_count
 * @property int|null $game_id
 * @property string|null $only_for
 * @property-read Game|null $game
 * @property-read Collection|Mod[] $mods
 * @property-read int|null $mod_count
 * @property-read Collection|Thread[] $threads
 * @property-read int|null $threads_count
 * @method static Builder|Tag whereGameId($value)
 * @method static Builder|Tag whereOnlyFor($value)
 * @property string|null $type
 * @method static Builder|Tag whereType($value)
 * @property-read int|null $mods_count
 * @mixin Eloquent
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Taggable
 *
 * @property int $id
 * @property int $tag_id
 * @property string $taggable_type
 * @property int $taggable_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Taggable newModelQuery()
 * @method static Builder|Taggable newQuery()
 * @method static Builder|Taggable query()
 * @method static Builder|Taggable whereCreatedAt($value)
 * @method static Builder|Taggable whereId($value)
 * @method static Builder|Taggable whereTagId($value)
 * @method static Builder|Taggable whereTaggableId($value)
 * @method static Builder|Taggable whereTaggableType($value)
 * @method static Builder|Taggable whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class Taggable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Thread
 *
 * @method static Builder|Thread newModelQuery()
 * @method static Builder|Thread newQuery()
 * @method static Builder|Thread query()
 * @property int $id
 * @property string $name
 * @property string $content
 * @property int $views
 * @property-read int|null $comment_count
 * @property bool $archived
 * @property string $bumped_at
 * @property string $pinned_at
 * @property int $forum_id
 * @property int|null $category_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Comment[] $comments
 * @method static Builder|Thread whereArchived($value)
 * @method static Builder|Thread whereBumpedAt($value)
 * @method static Builder|Thread whereCategoryId($value)
 * @method static Builder|Thread whereCommentsCount($value)
 * @method static Builder|Thread whereContent($value)
 * @method static Builder|Thread whereCreatedAt($value)
 * @method static Builder|Thread whereForumId($value)
 * @method static Builder|Thread whereId($value)
 * @method static Builder|Thread whereName($value)
 * @method static Builder|Thread wherePinnedAt($value)
 * @method static Builder|Thread whereUpdatedAt($value)
 * @method static Builder|Thread whereUserId($value)
 * @method static Builder|Thread whereViews($value)
 * @property int $last_user_id
 * @property-read Forum $forum
 * @property-read User $lastUser
 * @property-read User $user
 * @method static Builder|Thread whereLastUserId($value)
 * @property-read ForumCategory|null $category
 * @property bool $archived_by_mod
 * @method static Builder|Thread whereArchivedByMod($value)
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Subscription|null $subscribed
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read Collection|Taggable[] $tagsSpecial
 * @property-read int|null $tags_special_count
 * @property-read Collection|Report[] $reports
 * @property-read int|null $reports_count
 * @property bool $locked
 * @property bool $locked_by_mod
 * @method static Builder|Thread whereLocked($value)
 * @method static Builder|Thread whereLockedByMod($value)
 * @property bool $announce
 * @property Carbon|null $announce_until
 * @method static Builder|Thread whereAnnounce($value)
 * @method static Builder|Thread whereAnnounceUntil($value)
 * @property int|null $game_id
 * @property-read Game|null $game
 * @method static Builder|Thread whereCommentCount($value)
 * @method static Builder|Thread whereGameId($value)
 * @property-read int|null $comments_count
 * @mixin Eloquent
 */
	class Thread extends \Eloquent implements \App\Interfaces\SubscribableInterface {}
}

namespace App\Models{
/**
 * App\Models\TrackSession
 *
 * @property int $id
 * @property string $ip_address
 * @property int|null $user_id
 * @property Carbon|null $updated_at
 * @method static Builder|TrackSession newModelQuery()
 * @method static Builder|TrackSession newQuery()
 * @method static Builder|TrackSession query()
 * @method static Builder|TrackSession whereId($value)
 * @method static Builder|TrackSession whereIpAddress($value)
 * @method static Builder|TrackSession whereUpdatedAt($value)
 * @method static Builder|TrackSession whereUserId($value)
 * @mixin Eloquent
 */
	class TrackSession extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TransferRequest
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|TransferRequest newModelQuery()
 * @method static Builder|TransferRequest newQuery()
 * @method static Builder|TransferRequest query()
 * @method static Builder|TransferRequest whereCreatedAt($value)
 * @method static Builder|TransferRequest whereId($value)
 * @method static Builder|TransferRequest whereModId($value)
 * @method static Builder|TransferRequest whereUpdatedAt($value)
 * @method static Builder|TransferRequest whereUserId($value)
 * @property-read Mod $mod
 * @property-read User $user
 * @property int|null $keep_owner_level
 * @method static Builder|TransferRequest whereKeepOwnerLevel($value)
 * @mixin Eloquent
 */
	class TransferRequest extends \Eloquent {}
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
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @method static UserFactory factory(...$parameters)
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
 * @property-read UserExtra|null $extra
 * @property string $custom_color
 * @method static Builder|User whereCustomColor($value)
 * @property string|null $unique_name
 * @method static Builder|User whereUniqueName($value)
 * @property-read Ban|null $lastBan
 * @property-read Collection|BlockedTag[] $blockedTags
 * @property-read int|null $blocked_tags_count
 * @property-read Collection|User[] $blockedUsers
 * @property-read int|null $blocked_users_count
 * @property-read Collection|User[] $fullyBlockedUsers
 * @property-read int|null $fully_blocked_users_count
 * @property-read Collection|Mod[] $mods
 * @property-read int|null $mod_count
 * @property-read Collection|Game[] $followedGames
 * @property-read int|null $followed_games_count
 * @property-read Collection|Mod[] $followedMods
 * @property-read int|null $followed_mod_count
 * @property-read Collection|User[] $followedUsers
 * @property-read int|null $followed_users_count
 * @property \Illuminate\Support\Carbon|null $last_online
 * @property-read Collection|GameRole[] $allGameRoles
 * @property-read int|null $all_game_roles_count
 * @property-read Ban|null $ban
 * @property-read mixed $last_ban
 * @property-read Collection|Report[] $reports
 * @property-read int|null $reports_count
 * @method static Builder|User whereLastOnline($value)
 * @property string $banner
 * @property string $bio
 * @property bool $private_profile
 * @property string $custom_title
 * @property bool $invisible
 * @property string|null $donation_url
 * @property string $show_tag
 * @property-read Ban|null $gameBan
 * @property-read Collection|Ban[] $gameBans
 * @property-read int|null $game_bans_count
 * @property-read Collection|GameRole[] $gameRoles
 * @property-read int|null $game_roles_count
 * @property-read mixed $last_game_ban
 * @property-read Collection|SocialLogin[] $socialLogins
 * @property-read int|null $social_logins_count
 * @property-read Supporter|null $supporter
 * @method static Builder|User whereBanner($value)
 * @method static Builder|User whereBio($value)
 * @method static Builder|User whereCustomTitle($value)
 * @method static Builder|User whereDonationUrl($value)
 * @method static Builder|User whereInvisible($value)
 * @method static Builder|User wherePrivateProfile($value)
 * @method static Builder|User whereShowTag($value)
 * @property-read Collection|BlockedTag[] $allBlockedTags
 * @property-read int|null $all_blocked_tags_count
 * @property-read Collection|BlockedUser[] $allBlockedUsers
 * @property-read int|null $all_blocked_users_count
 * @property-read Collection|FollowedGame[] $allFollowedGames
 * @property-read int|null $all_followed_games_count
 * @property-read Collection|FollowedMod[] $allFollowedMods
 * @property-read int|null $all_followed_mod_count
 * @property-read Collection|FollowedUser[] $allFollowedUsers
 * @property-read int|null $all_followed_users_count
 * @property-read Collection|Comment[] $comments
 * @property-read int|null $comment_count
 * @property-read Collection|Thread[] $threads
 * @property-read int|null $threads_count
 * @property-read Collection|PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @property string|null $last_ip_address
 * @property bool $activated
 * @property string|null $waiting_email
 * @property string|null $pending_email
 * @property string|null $pending_email_set_at
 * @property-read int|null $all_followed_mods_count
 * @property-read int|null $followed_mods_count
 * @property-read int|null $mods_count
 * @method static Builder|User whereActivated($value)
 * @method static Builder|User whereLastIpAddress($value)
 * @method static Builder|User whereModCount($value)
 * @method static Builder|User wherePendingEmail($value)
 * @method static Builder|User wherePendingEmailSetAt($value)
 * @method static Builder|User whereWaitingEmail($value)
 * @property-read int|null $comments_count
 * @property-read TrackSession|null $trackSession
 * @mixin Eloquent
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * App\Models\UserCase
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $mod_user_id
 * @property bool $warning
 * @property string $reason
 * @property string|null $expire_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserCase newModelQuery()
 * @method static Builder|UserCase newQuery()
 * @method static Builder|UserCase query()
 * @method static Builder|UserCase whereCreatedAt($value)
 * @method static Builder|UserCase whereExpireDate($value)
 * @method static Builder|UserCase whereId($value)
 * @method static Builder|UserCase whereModUserId($value)
 * @method static Builder|UserCase whereReason($value)
 * @method static Builder|UserCase whereUpdatedAt($value)
 * @method static Builder|UserCase whereUserId($value)
 * @method static Builder|UserCase whereWarning($value)
 * @property string|null $pardon_reason
 * @property bool $pardoned
 * @property-read Ban|null $ban
 * @property-read User $user
 * @method static Builder|UserCase wherePardonReason($value)
 * @method static Builder|UserCase wherePardoned($value)
 * @property int|null $game_id
 * @property-read User|null $modUser
 * @method static Builder|UserCase whereGameId($value)
 * @property bool $active
 * @method static Builder|UserCase whereActive($value)
 * @mixin Eloquent
 */
	class UserCase extends \Eloquent {}
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
 * @method static Builder|UserExtra newModelQuery()
 * @method static Builder|UserExtra newQuery()
 * @method static Builder|UserExtra query()
 * @method static Builder|UserExtra whereBanner($value)
 * @method static Builder|UserExtra whereBio($value)
 * @method static Builder|UserExtra whereCreatedAt($value)
 * @method static Builder|UserExtra whereCustomTitle($value)
 * @method static Builder|UserExtra whereId($value)
 * @method static Builder|UserExtra whereLastOnline($value)
 * @method static Builder|UserExtra wherePrivateProfile($value)
 * @method static Builder|UserExtra whereUpdatedAt($value)
 * @method static Builder|UserExtra whereUserId($value)
 * @method static Builder|UserExtra whereLastOnlineAt($value)
 * @property Carbon|null $last_online
 * @property string|null $donation_url
 * @method static Builder|UserExtra whereDonationUrl($value)
 * @property string $default_mods_view
 * @property string $default_mods_sort
 * @property bool $home_show_last_games
 * @property bool $home_show_mods
 * @property bool $home_show_threads
 * @property bool $game_show_mods
 * @property bool $game_show_threads
 * @method static Builder|UserExtra whereDefaultModsSort($value)
 * @method static Builder|UserExtra whereDefaultModsView($value)
 * @method static Builder|UserExtra whereGameShowMods($value)
 * @method static Builder|UserExtra whereGameShowThreads($value)
 * @method static Builder|UserExtra whereHomeShowLastGames($value)
 * @method static Builder|UserExtra whereHomeShowMods($value)
 * @method static Builder|UserExtra whereHomeShowThreads($value)
 * @mixin Eloquent
 */
	class UserExtra extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserRecord
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $email
 * @property string|null $last_ip_address
 * @property array|null $social_logins
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserRecord newModelQuery()
 * @method static Builder|UserRecord newQuery()
 * @method static Builder|UserRecord query()
 * @method static Builder|UserRecord whereCreatedAt($value)
 * @method static Builder|UserRecord whereEmail($value)
 * @method static Builder|UserRecord whereId($value)
 * @method static Builder|UserRecord whereLastIpAddress($value)
 * @method static Builder|UserRecord whereSocialLogins($value)
 * @method static Builder|UserRecord whereUpdatedAt($value)
 * @method static Builder|UserRecord whereUserId($value)
 * @mixin Eloquent
 */
	class UserRecord extends \Eloquent {}
}

