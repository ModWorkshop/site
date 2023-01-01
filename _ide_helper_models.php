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
 * @property \Illuminate\Support\Carbon $expire_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Ban newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $case_id
 * @property-read \App\Models\UserCase|null $case
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereCaseId($value)
 * @property int|null $game_id
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereGameId($value)
 * @property bool $can_appeal
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereCanAppeal($value)
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedTag whereUserId($value)
 * @mixin \Eloquent
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereBlockUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereSilent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlockedUser whereUserId($value)
 * @mixin \Eloquent
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $mentions
 * @property-read int|null $mentions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\Subscription|null $subscribed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Report[] $reports
 * @property-read int|null $reports_count
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Mod|null $mod
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereDependableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereDependableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereOffsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dependency whereUrl($value)
 * @mixin \Eloquent
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereLastUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereUrlName($value)
 * @mixin \Eloquent
 * @property-read \App\Models\User $lastUser
 * @property-read \App\Models\Game|null $game
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
 * @property string $label
 * @property string $version
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|File whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereVersion($value)
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame query()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedGame whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\User $user
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod query()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereNotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedMod whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\User $user
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereFollowUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereNotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FollowedUser whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\User $user
 */
	class FollowedUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Forum
 *
 * @property int $id
 * @property int|null $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Forum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum query()
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Game|null $game
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Thread[] $threads
 * @property-read int|null $threads_count
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ForumCategory[] $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereName($value)
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $emoji
 * @property-read \App\Models\Forum $forum
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereEmoji($value)
 * @property bool $is_private
 * @property bool $private_threads
 * @property bool $banned_can_post
 * @property string|null $announce_until
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GameRole[] $gameRoles
 * @property-read int|null $game_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereAnnounceUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereBannedCanPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory wherePrivateThreads($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereButtons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereDisporder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereLastDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereWebhookUrl($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Forum|null $forum
 * @property int|null $forum_id
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereForumId($value)
 * @property-read \App\Models\FollowedGame|null $followed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mod[] $mods
 * @property-read int|null $mods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GameRole[] $roles
 * @property-read int|null $roles_count
 * @property-read mixed $user_data
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereModsCount($value)
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_vanity
 * @property-read \App\Models\Game $game
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereIsVanity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRole whereUpdatedAt($value)
 * @mixin \Eloquent
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
 * @property-read \App\Models\Mod $mod
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Dependency[] $dependencies
 * @property-read int|null $dependencies_count
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereLocalized($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstructsTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Game $game
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Mod $mod
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Link newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link query()
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereVersion($value)
 * @mixin \Eloquent
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Mention newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mention newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mention query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mention whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mention whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mention whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mention whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mention whereUserId($value)
 * @mixin \Eloquent
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
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Game|null $game
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\ModFactory factory(...$parameters)
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
 * @method static Builder|Mod whereUserId($value)
 * @property-read \App\Models\Image|null $thumbnail
 * @property int|null $banner_id
 * @property-read \App\Models\Image|null $banner
 * @property-read bool $liked
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Link[] $links
 * @property-read int|null $links_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ModMember[] $members
 * @property-read int|null $members_count
 * @method static Builder|Mod whereBannerId($value)
 * @method static Builder|Mod whereLikes($value)
 * @method static Builder|Mod whereBumpedAt($value)
 * @method static Builder|Mod wherePublishedAt($value)
 * @property-read \App\Models\TransferRequest|null $transferRequest
 * @property int|null $last_user_id
 * @property-read \App\Models\User|null $lastUser
 * @method static Builder|Mod whereLastUserId($value)
 * @property string $access_ids
 * @property-read \App\Models\Follows|null $follows
 * @property-read \App\Models\Follows|null $followsUsingCategory
 * @property-read \App\Models\Suspension|null $lastSuspension
 * @property-read \App\Models\FollowedMod|null $followed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taggable[] $tagsSpecial
 * @property-read int|null $tags_special_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FollowedMod[] $followers
 * @property-read int|null $followers_count
 * @property-read \App\Models\Subscription|null $subscribed
 * @property bool $has_download
 * @property bool $approved
 * @method static Builder|Mod whereApproved($value)
 * @method static Builder|Mod whereHasDownload($value)
 * @property int|null $instructs_template_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Dependency[] $dependencies
 * @property-read int|null $dependencies_count
 * @property-read \App\Models\InstructsTemplate|null $instructsTemplate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $membersThatCanEdit
 * @property-read int|null $members_that_can_edit_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Report[] $reports
 * @property-read int|null $reports_count
 * @method static Builder|Mod whereInstructsTemplateId($value)
 * @property float $daily_score
 * @property float $weekly_score
 * @method static \Illuminate\Database\Eloquent\Builder|Mod whereDailyScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mod whereWeeklyScore($value)
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModDownload whereUserId($value)
 * @mixin \Eloquent
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModLike whereUserId($value)
 * @mixin \Eloquent
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereUserId($value)
 * @mixin \Eloquent
 * @property bool $accepted
 * @method static \Illuminate\Database\Eloquent\Builder|ModMember whereAccepted($value)
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModView query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModView whereUserId($value)
 * @mixin \Eloquent
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereContextType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Model|\Eloquent $context
 * @property-read Model|\Eloquent $notifiable
 * @property-read \App\Models\User $user
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUserId($value)
 * @property int|null $from_user_id
 * @property-read \App\Models\User|null $fromUser
 * @method static Builder|Notification whereFromUserId($value)
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
 * @property string|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereType($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Mod $mod
 * @property-read \App\Models\User $user
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PopularityLog whereUpdatedAt($value)
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReportableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReportableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $name
 * @property int|null $game_id
 * @property-read Model|\Eloquent $reportable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereName($value)
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
 * @property bool $is_vanity
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereIsVanity($value)
 */
	class Role extends \Eloquent {}
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValue($value)
 * @mixin \Eloquent
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereSpecialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereUserId($value)
 * @mixin \Eloquent
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSubscribableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSubscribableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\User $user
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Supporter
 *
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $expire_date
 * @property bool $is_cancelled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereIsCancelled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supporter whereUserId($value)
 * @mixin \Eloquent
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension query()
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereModUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suspension whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Mod $mod
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Game[] $games
 * @property-read int|null $games_count
 * @property int|null $game_id
 * @property string|null $only_for
 * @property-read \App\Models\Game|null $game
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mod[] $mods
 * @property-read int|null $mods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Thread[] $threads
 * @property-read int|null $threads_count
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereOnlyFor($value)
 * @property string|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereType($value)
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Taggable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Taggable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Taggable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Taggable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taggable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taggable whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taggable whereTaggableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taggable whereTaggableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Taggable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Taggable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Thread
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Thread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Thread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Thread query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $content
 * @property int $views
 * @property-read int|null $comments_count
 * @property bool $archived
 * @property string $bumped_at
 * @property string $pinned_at
 * @property int $forum_id
 * @property int|null $category_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereBumpedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread wherePinnedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereViews($value)
 * @property int $last_user_id
 * @property-read \App\Models\Forum $forum
 * @property-read \App\Models\User $lastUser
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereLastUserId($value)
 * @property-read \App\Models\ForumCategory|null $category
 * @property bool $archived_by_mod
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereArchivedByMod($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \App\Models\Subscription|null $subscribed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taggable[] $tagsSpecial
 * @property-read int|null $tags_special_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Report[] $reports
 * @property-read int|null $reports_count
 * @property bool $locked
 * @property bool $locked_by_mod
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereLockedByMod($value)
 * @property bool $announce
 * @property \Illuminate\Support\Carbon|null $announce_until
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereAnnounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thread whereAnnounceUntil($value)
 */
	class Thread extends \Eloquent implements \App\Interfaces\SubscribableInterface {}
}

namespace App\Models{
/**
 * App\Models\TransferRequest
 *
 * @property int $id
 * @property int $mod_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereModId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Mod $mod
 * @property-read \App\Models\User $user
 * @property int|null $keep_owner_level
 * @method static \Illuminate\Database\Eloquent\Builder|TransferRequest whereKeepOwnerLevel($value)
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
 * @property string $custom_color
 * @method static Builder|User whereCustomColor($value)
 * @property string|null $unique_name
 * @method static Builder|User whereUniqueName($value)
 * @property-read \App\Models\Ban|null $lastBan
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BlockedTag[] $blockedTags
 * @property-read int|null $blocked_tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $blockedUsers
 * @property-read int|null $blocked_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $fullyBlockedUsers
 * @property-read int|null $fully_blocked_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mod[] $mods
 * @property-read int|null $mods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Game[] $followedGames
 * @property-read int|null $followed_games_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mod[] $followedMods
 * @property-read int|null $followed_mods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $followedUsers
 * @property-read int|null $followed_users_count
 * @property \Illuminate\Support\Carbon|null $last_online
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GameRole[] $allGameRoles
 * @property-read int|null $all_game_roles_count
 * @property-read \App\Models\Ban|null $ban
 * @property-read mixed $last_ban
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Report[] $reports
 * @property-read int|null $reports_count
 * @method static Builder|User whereLastOnline($value)
 * @property string $banner
 * @property string $bio
 * @property bool $private_profile
 * @property string $custom_title
 * @property bool $invisible
 * @property string|null $donation_url
 * @property string $show_tag
 * @property-read \App\Models\Ban|null $gameBan
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ban[] $gameBans
 * @property-read int|null $game_bans_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GameRole[] $gameRoles
 * @property-read int|null $game_roles_count
 * @property-read mixed $last_game_ban
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SocialLogin[] $socialLogins
 * @property-read int|null $social_logins_count
 * @property-read \App\Models\Supporter|null $supporter
 * @method static Builder|User whereBanner($value)
 * @method static Builder|User whereBio($value)
 * @method static Builder|User whereCustomTitle($value)
 * @method static Builder|User whereDonationUrl($value)
 * @method static Builder|User whereInvisible($value)
 * @method static Builder|User wherePrivateProfile($value)
 * @method static Builder|User whereShowTag($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BlockedTag[] $allBlockedTags
 * @property-read int|null $all_blocked_tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BlockedUser[] $allBlockedUsers
 * @property-read int|null $all_blocked_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FollowedGame[] $allFollowedGames
 * @property-read int|null $all_followed_games_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FollowedMod[] $allFollowedMods
 * @property-read int|null $all_followed_mods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FollowedUser[] $allFollowedUsers
 * @property-read int|null $all_followed_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Thread[] $threads
 * @property-read int|null $threads_count
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereExpireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereModUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereWarning($value)
 * @mixin \Eloquent
 * @property string|null $pardon_reason
 * @property bool $pardoned
 * @property-read \App\Models\Ban|null $ban
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase wherePardonReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase wherePardoned($value)
 * @property int|null $game_id
 * @property-read \App\Models\User|null $modUser
 * @method static \Illuminate\Database\Eloquent\Builder|UserCase whereGameId($value)
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
 * @property string|null $donation_url
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereDonationUrl($value)
 * @property string $default_mods_view
 * @property string $default_mods_sort
 * @property bool $home_show_last_games
 * @property bool $home_show_mods
 * @property bool $home_show_threads
 * @property bool $game_show_mods
 * @property bool $game_show_threads
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereDefaultModsSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereDefaultModsView($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereGameShowMods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereGameShowThreads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereHomeShowLastGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereHomeShowMods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserExtra whereHomeShowThreads($value)
 */
	class UserExtra extends \Eloquent {}
}

