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
    total_replies: number,
    replying_comment?: Comment | null;
}

export interface File {
    id: number;
    user_id: number;
    mod_id: number;
    name: string;
    version: string;
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
    user: User;
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

export interface ModMember extends User {
    level: number,
    accepted: boolean,
}

export interface Mod {
    id: number;
    breadcrumb: Array<Breadcrumb>,
    thumbnail_id: number | null;
    banner_id?: number;
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
    bumped_at: string | null;
    published_at: string | null;
    created_at: string | null;
    updated_at: string | null;
    download_id: number | null;
    download_type: string | null;
    user?: User | null;
    category?: Category | null;
    game?: Category | null;
    thumbnail?: Image;
    banner?: Image;
    tags?: Array<Tag> | null;
    images?: Array<Image> | null;
    files?: Array<File> | null;
    links?: Array<Link> | null;
    comments?: Array<Comment> | null;
    download?: File|Link;
    tags_count?: number | null;
    images_count?: number | null;
    files_count?: number | null;
    comments_count?: number | null;
    members: Array<ModMember>,
    transfer_request: TransferRequest,
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

export interface Role {
    id: number;
    name: string;
    tag: string;
    desc: string;
    color: string;
    order: number;
    created_at: string | null;
    updated_at: string | null;
    permissions?: Record<string, boolean>
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
    donation_url: string
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
}

export interface TransferRequest {
    id: number;
    keep_owner_level: number | null;
    mod_id: number;
    user_id: number;
    created_at: string | null;
    updated_at: string | null;
    mod?: Mod | null;
    user?: User | null;
}

export interface Notification {
    id: number;
    notifiable_type: string;
    notifiable_id: number;
    context_type: string;
    context_id: number;
    type: string;
    seen: boolean;
    data: Array<any> | any | null;
    created_at: string | null;
    updated_at: string | null;
    user_id: number;
    notifiable?: any | null;
    context?: any | null;
    user?: User | null;
}