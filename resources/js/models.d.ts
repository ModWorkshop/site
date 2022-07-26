/**
 * This file is auto generated using 'php artisan typescript:generate'
 *
 * Changes to this file will be lost when the command is run again
 */

declare namespace App.Models {
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
        game?: App.Models.Category | null;
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
    }

    export interface Mod {
        id: number;
        thumbnail_id: number | null;
        category_id: number | null;
        game_id: number | null;
        submitter_id: number;
        name: string;
        desc: string;
        short_desc: string;
        changelog: string;
        license: string;
        instructions: string;
        depends_on: Array<any> | any | null;
        visibility: number;
        banner: string;
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
        bump_date: string | null;
        publish_date: string | null;
        created_at: string | null;
        updated_at: string | null;
        download_id: number | null;
        download_type: string | null;
        submitter?: App.Models.User | null;
        category?: App.Models.Category | null;
        game?: App.Models.Category | null;
        thumbnail?: App.Models.Image | null;
        tags?: Array<App.Models.Tag> | null;
        images?: Array<App.Models.Image> | null;
        files?: Array<App.Models.File> | null;
        comments?: Array<App.Models.Comment> | null;
        download?: any | null;
        tags_count?: number | null;
        images_count?: number | null;
        files_count?: number | null;
        comments_count?: number | null;
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

    export interface Section {
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
        readonly path?: any;
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
        categories?: Array<App.Models.Category> | null;
        sections?: Array<App.Models.Section> | null;
        categories_count?: number | null;
        sections_count?: number | null;
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
        extra?: App.Models.UserExtra | null;
        roles?: Array<App.Models.Role> | null;
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
    }

    export interface UsersRolesLink {}

}
