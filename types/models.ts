/**
 * This file is auto generated using 'php artisan typescript:generate'
 *
 * Changes to this file will be lost when the command is run again
 */

export interface Category {
    id: number;
    name: string;
    short_name: string | null;
    desc: string;
    hidden: boolean;
    grid: boolean;
    disporder: number;
    parent_id: number | null;
    game_id: number;
    thumbnail?: string;
    banner: string;
    buttons: string;
    webhook_url: string;
    approval_only: boolean;
    last_date: string;
    created_at?: string;
    updated_at?: string;
    game?: Category | null;
    parent?: Category | null;
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
    user?: User | null;
    commentable?: any | null;
    last_replies?: any | null;
    replying_comment?: Comment | null;
}

export interface File {
    id: number;
    user_id: number;
    mod_id: number;
    name: string;
    desc: string;
    label: string;
    file: string;
    type: string;
    image_id: number | null;
    size: number;
    approved: boolean;
    created_at: string | null;
    updated_at: string | null;
    mod?: Mod | null;
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
    breadcrumb: Array<Breadcrumb>,
    thumbnail_id: number | null;
    banner_id?: number;
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
    legacy_banner_url: string;
    url: string;
    downloads: number;
    likes: number;
    views: number;
    version: string;
    donation: string;
    access_ids: string;
    suspended: boolean;
    liked: boolean;
    comments_disabled: boolean;
    file_status: number;
    score: number;
    bump_date: string | null;
    publish_date: string | null;
    created_at: string | null;
    updated_at: string | null;
    download_id: number | null;
    download_type: string | null;
    submitter?: User | null;
    category?: Category | null;
    game?: Category | null;
    thumbnail?: Image;
    banner?: Image;
    tags?: Array<Tag> | null;
    images?: Array<Image> | null;
    files?: Array<File> | null;
    comments?: Array<Comment> | null;
    download?: any | null;
    tags_count?: number | null;
    images_count?: number | null;
    files_count?: number | null;
    comments_count?: number | null;
}

export interface Breadcrumb {
    href: string,
    name: string
}

export interface Permission {
    id: number;
    slug: string;
    name: string;
    desc: string;
    created_at: string | null;
    updated_at: string | null;
    allow: boolean;
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
    permissions?: Array<Permission> | null;
}

export interface Game {
    id: number;
    name: string;
    short_name?: string;
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
    user?: User | null;
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
    categories?: Array<Category> | null;
    sections?: Array<Game> | null;
    categories_count?: number | null;
    sections_count?: number | null;
}

export interface User {
    id: number;
    name: string;
    unique_name: string;
    email: string | null;
    email_verified_at: string | null;
    created_at: string | null;
    updated_at: string | null;
    avatar: string;
    roles?: Array<Role> | null;
    roles_count?: number | null;
    role_ids: Array<number>;
    custom_color: string;
    color: string;
    tag: string;
    readonly role_names?: any;
    readonly permissions?: any;
    banner: string;
    bio: string;
    private_profile: boolean;
    custom_title: string;
    last_online: string | null;
    user_id: number | null;
}

export interface UsersRolesLink {}
