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
    mentions: User[],
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
    breadcrumb?: Array<Breadcrumb>,
    thumbnail_id?: number;
    banner_id?: number;
    category_id?: number;
    game_id: number;
    user_id: number;
    name: string;
    desc: string;
    short_desc: string;
    changelog: string;
    license: string;
    instructions: string;
    depends_on?: unknown;
    visibility: number;
    legacy_banner_url: string;
    downloads: number;
    likes: number;
    views: number;
    version: string;
    donation: string;
    suspended: boolean;
    liked?: boolean;
    comments_disabled: boolean;
    file_status: number;
    bumped_at?: string;
    published_at?: string;
    created_at?: string;
    updated_at?: string;
    download_id?: number;
    download_type?: string;
    user?: User;
    last_user?: User;
    category?: Category;
    game?: Category;
    thumbnail?: Image;
    banner?: Image;
    tags?: Array<Tag>;
    images?: Array<Image>;
    files?: Array<File>;
    links?: Array<Link>;
    comments?: Array<Comment>;
    download?: File|Link;
    tags_count?: number;
    images_count?: number;
    files_count?: number;
    comments_count?: number;
    members: Array<ModMember>,
    transfer_request?: TransferRequest,
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
    email_verified_at?: string;
    created_at?: string;
    updated_at?: string;
    avatar: string;
    roles?: Array<Role>;
    roles_count?: number;
    role_ids: Array<number>;
    custom_color: string;
    color: string;
    tag: string;
    readonly role_names?: string[];
    readonly permissions?: string[];
    banner: string;
    bio: string;
    private_profile: boolean;
    custom_title: string;
    last_online?: string;
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
    image_id?: number;
    created_at?: string;
    updated_at?: string;
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