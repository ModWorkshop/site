/**
 * This file is auto generated using 'php artisan typescript:generate'
 *
 * Changes to this file will be lost when the command is run again
 */

declare namespace App.Models {
    export interface Ban {
        id: number;
        user_id: number | null;
        expire_date: string | null;
        created_at: string | null;
        updated_at: string | null;
        case_id: number | null;
        user?: App.Models.User | null;
        case?: App.Models.UserCase | null;
    }

    export interface BlockedTag {
        id: number;
        user_id: number;
        tag_id: number;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface BlockedUser {
        id: number;
        user_id: number;
        block_user_id: number;
        silent: boolean;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface Category {
        id: number;
        name: string;
        short_name: string | null;
        desc: string;
        hidden: boolean;
        grid: boolean;
        disporder: number;
        parent_id: number | null;
        game_id: number | null;
        thumbnail: string;
        banner: string;
        buttons: string;
        webhook_url: string;
        approval_only: boolean;
        last_date: string;
        created_at: string | null;
        updated_at: string | null;
        game?: App.Models.Game | null;
        parent?: App.Models.Category | null;
        readonly path?: any;
    }

    export interface Comment {
        id: number;
        commentable_type: string;
        commentable_id: number;
        user_id: number;
        content: string;
        pinned: boolean;
        reply_to: number | null;
        created_at: string | null;
        updated_at: string | null;
        user?: App.Models.User | null;
        commentable?: any | null;
        replies?: Array<App.Models.Comment> | null;
        replying_comment?: App.Models.Comment | null;
        mentions?: any | null;
        replies_count?: number | null;
    }

    export interface Dependency {
        id: number;
        name: string | null;
        url: string | null;
        mod_id: number | null;
        optional: boolean;
        dependable_type: string;
        dependable_id: number;
        order: number;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface Document {
        id: number;
        name: string;
        url_name: string;
        desc: string;
        game_id: number | null;
        last_user_id: number;
        created_at: string | null;
        updated_at: string | null;
        last_user?: App.Models.User | null;
    }

    export interface File {
        id: number;
        user_id: number;
        mod_id: number;
        name: string;
        desc: string;
        file: string;
        type: string;
        image_id: number | null;
        size: number;
        approved: boolean;
        created_at: string | null;
        updated_at: string | null;
        label: string;
        version: string;
        mod?: App.Models.Mod | null;
        user?: App.Models.User | null;
    }

    export interface FollowedGame {
        id: number;
        user_id: number;
        game_id: number;
        created_at: string | null;
        updated_at: string | null;
        user?: App.Models.User | null;
    }

    export interface FollowedMod {
        id: number;
        user_id: number;
        mod_id: number;
        notify: boolean;
        created_at: string | null;
        updated_at: string | null;
        user?: App.Models.User | null;
    }

    export interface FollowedUser {
        id: number;
        user_id: number;
        follow_user_id: number;
        notify: boolean;
        created_at: string | null;
        updated_at: string | null;
        user?: App.Models.User | null;
    }

    export interface Forum {
        id: number;
        name: string;
        game_id: number | null;
        created_at: string | null;
        updated_at: string | null;
        game?: App.Models.Game | null;
        threads?: Array<App.Models.Thread> | null;
        categories?: Array<App.Models.ForumCategory> | null;
        threads_count?: number | null;
        categories_count?: number | null;
    }

    export interface ForumCategory {
        id: number;
        name: string;
        desc: string;
        forum_id: number;
        created_at: string | null;
        updated_at: string | null;
        emoji: string;
        forum?: App.Models.Forum | null;
    }

    export interface Game {
        id: number;
        name: string;
        short_name: string | null;
        disporder: number;
        thumbnail: string;
        banner: string;
        buttons: string;
        webhook_url: string;
        last_date: string;
        created_at: string | null;
        updated_at: string | null;
        forum_id: number | null;
        forum?: App.Models.Forum | null;
        followed?: App.Models.FollowedGame | null;
        readonly breadcrumb?: any;
    }

    export interface Image {
        id: number;
        user_id: number;
        has_thumb: boolean;
        file: string;
        type: string;
        size: number;
        created_at: string | null;
        updated_at: string | null;
        mod_id: number;
        mod?: App.Models.Mod | null;
    }

    export interface InstructsTemplate {
        id: number;
        name: string;
        instructions: string;
        localized: boolean;
        game_id: number;
        created_at: string | null;
        updated_at: string | null;
        dependencies?: Array<App.Models.Dependency> | null;
        dependencies_count?: number | null;
    }

    export interface Link {
        id: number;
        user_id: number;
        mod_id: number;
        name: string;
        desc: string;
        label: string;
        url: string;
        version: string;
        image_id: number | null;
        created_at: string | null;
        updated_at: string | null;
        mod?: App.Models.Mod | null;
        user?: App.Models.User | null;
    }

    export interface Mention {
        id: number;
        user_id: number;
        comment_id: number;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface Mod {
        id: number;
        thumbnail_id: number | null;
        category_id: number | null;
        game_id: number | null;
        user_id: number;
        name: string;
        desc: string;
        short_desc: string;
        changelog: string;
        license: string;
        instructions: string;
        depends_on: Array<any> | any | null;
        visibility: number;
        legacy_banner_url: string;
        url: string;
        downloads: number;
        views: number;
        version: string;
        donation: string;
        access_ids: string;
        suspended: boolean;
        comments_disabled: boolean;
        score: number;
        bumped_at: string | null;
        published_at: string | null;
        created_at: string | null;
        updated_at: string | null;
        download_id: number | null;
        download_type: string | null;
        likes: number;
        banner_id: number | null;
        last_user_id: number | null;
        has_download: boolean;
        approved: boolean | null;
        instructs_template_id: number | null;
        followers?: Array<App.Models.FollowedMod> | null;
        user?: App.Models.User | null;
        last_user?: App.Models.User | null;
        category?: App.Models.Category | null;
        game?: App.Models.Game | null;
        thumbnail?: App.Models.Image | null;
        banner?: App.Models.Image | null;
        tags_special?: Array<App.Models.Taggable> | null;
        tags?: Array<App.Models.Tag> | null;
        images?: Array<App.Models.Image> | null;
        files?: Array<App.Models.File> | null;
        links?: Array<App.Models.Link> | null;
        members?: any | null;
        dependencies?: Array<App.Models.Dependency> | null;
        instructs_template?: App.Models.InstructsTemplate | null;
        members_that_can_edit?: any | null;
        comments?: Array<App.Models.Comment> | null;
        transfer_request?: App.Models.TransferRequest | null;
        download?: any | null;
        liked?: App.Models.ModLike | null;
        followed?: App.Models.FollowedMod | null;
        last_suspension?: App.Models.Suspension | null;
        followers_count?: number | null;
        tags_special_count?: number | null;
        tags_count?: number | null;
        images_count?: number | null;
        files_count?: number | null;
        links_count?: number | null;
        dependencies_count?: number | null;
        comment_count?: number | null;
        readonly breadcrumb?: any;
    }

    export interface ModDownload {
        id: number;
        mod_id: number;
        user_id: number | null;
        ip_address: string;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface ModLike {
        id: number;
        mod_id: number;
        user_id: number;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface ModMember {
        id: number;
        level: number;
        accepted: boolean;
        mod_id: number;
        user_id: number | null;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface ModView {
        id: number;
        mod_id: number;
        user_id: number | null;
        ip_address: string;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface Notification {
        id: number;
        notifiable_type: string;
        notifiable_id: number;
        context_type: string | null;
        context_id: number | null;
        type: string;
        seen: boolean;
        data: Array<any> | any | null;
        created_at: string | null;
        updated_at: string | null;
        user_id: number;
        from_user_id: number | null;
        notifiable?: any | null;
        context?: any | null;
        user?: App.Models.User | null;
        from_user?: App.Models.User | null;
    }

    export interface Permission {
        id: number;
        name: string;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface Report {
        id: number;
        name: string | null;
        data: Array<any> | any;
        user_id: number;
        game_id: number | null;
        reason: string;
        archived: boolean;
        reportable_type: string;
        reportable_id: number;
        created_at: string | null;
        updated_at: string | null;
        user?: App.Models.User | null;
        reportable?: any | null;
    }

    export interface Role {
        id: number;
        name: string;
        tag: string;
        desc: string;
        color: string;
        order: number;
        created_at: string | null;
        updated_at: string | null;
        permissions?: any | null;
    }

    export interface Setting {
        id: number;
        name: string;
        type: string;
        value: string;
        public: boolean;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface SocialLogin {
        id: number;
        user_id: number | null;
        social_id: string;
        special_id: string;
        created_at: string | null;
        updated_at: string | null;
        user?: App.Models.User | null;
    }

    export interface Subscription {
        id: number;
        user_id: number;
        subscribable_type: string;
        subscribable_id: number;
        created_at: string | null;
        updated_at: string | null;
        user?: App.Models.User | null;
    }

    export interface Suspension {
        id: number;
        mod_id: number;
        mod_user_id: number | null;
        reason: string;
        status: boolean;
        created_at: string | null;
        updated_at: string | null;
        mod?: App.Models.Mod | null;
    }

    export interface Tag {
        id: number;
        name: string;
        color: string;
        notice: string;
        notice_localized: boolean;
        created_at: string | null;
        updated_at: string | null;
        game_id: number | null;
        type: string | null;
        notice_type: string | null;
        game?: App.Models.Game | null;
        mods?: Array<App.Models.Mod> | null;
        threads?: Array<App.Models.Thread> | null;
        mod_count?: number | null;
        threads_count?: number | null;
    }

    export interface Taggable {
        id: number;
        tag_id: number;
        taggable_type: string;
        taggable_id: number;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface Thread {
        id: number;
        name: string;
        content: string;
        views: number;
        comment_count: number;
        archived: boolean;
        bumped_at: string | null;
        pinned_at: string | null;
        forum_id: number;
        category_id: number | null;
        user_id: number;
        last_user_id: number;
        created_at: string | null;
        updated_at: string | null;
        archived_by_mod: boolean;
        user?: App.Models.User | null;
        last_user?: App.Models.User | null;
        forum?: App.Models.Forum | null;
        category?: App.Models.ForumCategory | null;
        comments?: Array<App.Models.Comment> | null;
        tags?: Array<App.Models.Tag> | null;
        tags_special?: Array<App.Models.Taggable> | null;
        comment_count?: number | null;
        tags_count?: number | null;
        tags_special_count?: number | null;
    }

    export interface TransferRequest {
        id: number;
        keep_owner_level: number | null;
        mod_id: number;
        user_id: number;
        created_at: string | null;
        updated_at: string | null;
        mod?: App.Models.Mod | null;
        user?: App.Models.User | null;
    }

    export interface User {
        id: number;
        name: string;
        email: string | null;
        email_verified_at: string | null;
        password: string | null;
        remember_token: string | null;
        created_at: string | null;
        updated_at: string | null;
        avatar: string;
        custom_color: string;
        unique_name: string | null;
        last_online: string | null;
        followed_games?: any | null;
        followed_mods?: any | null;
        followed_users?: any | null;
        fully_blocked_users?: any | null;
        blocked_users?: any | null;
        blocked_tags?: any | null;
        mods?: Array<App.Models.Mod> | null;
        extra?: App.Models.UserExtra | null;
        roles?: any | null;
        ban?: App.Models.Ban | null;
        mod_count?: number | null;
        readonly role_names?: any;
        readonly permissions?: any;
        readonly last_ban?: any;
    }

    export interface UserCase {
        id: number;
        user_id: number;
        mod_user_id: number | null;
        warning: boolean;
        reason: string;
        expire_date: string | null;
        created_at: string | null;
        updated_at: string | null;
        pardon_reason: string | null;
        pardoned: boolean;
        ban?: App.Models.Ban | null;
        user?: App.Models.User | null;
    }

    export interface UserExtra {
        id: number;
        banner: string;
        bio: string;
        private_profile: boolean;
        custom_title: string;
        last_online: string | null;
        user_id: number | null;
        created_at: string | null;
        updated_at: string | null;
        donation_url: string | null;
    }

}
