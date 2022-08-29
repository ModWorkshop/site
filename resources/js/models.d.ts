/**
 * This file is auto generated using 'php artisan typescript:generate'
 *
 * Changes to this file will be lost when the command is run again
 */

declare namespace App.Models {
    export interface Ban {
        id: number;
        user_id: number | null;
        reason: string;
        expire_date: string;
        created_at: string | null;
        updated_at: string | null;
        user?: App.Models.User | null;
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
        last_replies?: any | null;
        replying_comment?: App.Models.Comment | null;
        mentions?: any | null;
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
        file_status: number;
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
        user?: App.Models.User | null;
        last_user?: App.Models.User | null;
        category?: App.Models.Category | null;
        game?: App.Models.Game | null;
        thumbnail?: App.Models.Image | null;
        banner?: App.Models.Image | null;
        tags?: Array<App.Models.Tag> | null;
        images?: Array<App.Models.Image> | null;
        files?: Array<App.Models.File> | null;
        links?: Array<App.Models.Link> | null;
        members?: Array<App.Models.User> | null;
        comments?: Array<App.Models.Comment> | null;
        transfer_request?: App.Models.TransferRequest | null;
        download?: any | null;
        liked?: App.Models.ModLike | null;
        tags_count?: number | null;
        images_count?: number | null;
        files_count?: number | null;
        links_count?: number | null;
        members_count?: number | null;
        comments_count?: number | null;
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
        notifiable?: any | null;
        context?: any | null;
        user?: App.Models.User | null;
    }

    export interface Permission {
        id: number;
        slug: string;
        name: string;
        desc: string;
        created_at: string | null;
        updated_at: string | null;
    }

    export interface PermissionsRolesLink {}

    export interface Role {
        id: number;
        name: string;
        tag: string;
        desc: string;
        color: string;
        order: number;
        created_at: string | null;
        updated_at: string | null;
        permissions?: Array<App.Models.Permission> | null;
        permissions_count?: number | null;
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

    export interface Tag {
        id: number;
        name: string;
        color: string;
        notice: string;
        notice_type: number;
        notice_localized: boolean;
        created_at: string | null;
        updated_at: string | null;
        game_id: number | null;
        only_for: string | null;
        game?: App.Models.Game | null;
        mods?: Array<App.Models.Mod> | null;
        threads?: Array<App.Models.Thread> | null;
        mods_count?: number | null;
        threads_count?: number | null;
    }

    export interface Thread {
        id: number;
        name: string;
        content: string;
        views: number;
        comments_count: number;
        archived: boolean;
        bumped_at: string | null;
        pinned_at: string | null;
        forum_id: number;
        category_id: number | null;
        user_id: number;
        last_user_id: number;
        created_at: string | null;
        updated_at: string | null;
        user?: App.Models.User | null;
        last_user?: App.Models.User | null;
        forum?: App.Models.Forum | null;
        category?: App.Models.ForumCategory | null;
        comments?: Array<App.Models.Comment> | null;
        comments_count?: number | null;
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
        extra?: App.Models.UserExtra | null;
        roles?: Array<App.Models.Role> | null;
        last_ban?: App.Models.Ban | null;
        roles_count?: number | null;
        readonly role_names?: any;
        readonly permissions?: any;
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

    export interface UsersRolesLink {}

}
