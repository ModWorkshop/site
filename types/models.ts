import { Paginator } from './paginator';
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
    created_at?: string;
    updated_at?: string;
    user?: User | null;
    commentable?: Mod|Thread;
    last_replies?: Comment[];
    total_replies?: number;
    replying_comment?: Comment | null;
    subscribed?: boolean;
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
    created_at?: string;
    updated_at?: string;
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
    created_at?: string;
    updated_at?: string;
    mod_id: number;
}

export interface ModMember extends User {
    level: number,
    accepted: boolean,
}

export interface Mod {
    id: number;
    breadcrumb?: Array<Breadcrumb>;
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
    has_download: boolean;
    approved: boolean;
    bumped_at?: string;
    published_at?: string;
    created_at?: string;
    updated_at?: string;
    download_id?: number;
    download_type?: string;
    user?: User;
    last_user?: User;
    category?: Category;
    game?: Game;
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
    transfer_request?: TransferRequest;
    tag_ids?: number[];
    last_suspension?: Suspension;
    followed?: { notify: boolean };
    subscribed?: boolean;
    send_for_approval?: boolean;
    dependencies?: Dependency[],
    instructs_template_id?: number
    instructs_template?: InstructsTemplate
}

export interface Breadcrumb {
    id?: string|number,
    type?: string,
    attachToPrev?: string,
    to?: string,
    name?: string,
}

export interface Permission {
    id: number;
    name: string;
    created_at?: string;
    updated_at?: string;
    type?: 'game'|'global';
    allow: boolean;
}

export interface Role {
    id: number;
    name: string;
    tag: string;
    desc: string;
    color: string;
    order: number;
    created_at?: string;
    updated_at?: string;
    is_vanity: boolean;
    permissions?: Record<string, boolean>
}

export interface Game {
    id: number;
    forum_id: number;
    name: string;
    short_name?: string;
    thumbnail: string;
    banner: string;
    buttons: string;
    webhook_url: string;
    last_date: string;
    created_at?: string;
    updated_at?: string;
    forum?: Forum,
    path?: string;
    followed?: boolean;
    user_data?: GameUserData
}

export interface SocialLogin {
    id: number;
    user_id: number | null;
    social_id: string;
    special_id: string;
    created_at?: string;
    updated_at?: string;
    user?: User | null;
}

export interface Tag {
    id: number;
    game_id?: number;
    name: string;
    color: string;
    notice: string;
    notice_type: string;
    notice_localized: boolean;
    created_at?: string;
    updated_at?: string;
    categories?: Array<Category> | null;
    sections?: Array<Game> | null;
    categories_count?: number | null;
    sections_count?: number | null;
    type: string;
}

export interface Ban {
    id: number;
    user_id: number;
    created_at: string;
    updated_at: string;
    case_id: number;
    user: User;
    case: UserCase;
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
    roles?: Role[];
    roles_count?: number;
    role_ids: number[];
    game_role_ids: number[];
    custom_color: string;
    color: string;
    tag: string;
    readonly role_names?: Record<string, boolean>;
    readonly permissions?: Record<string, boolean>;
    banner: string;
    bio: string;
    private_profile: boolean;
    invisible: boolean;
    custom_title: string;
    last_online?: string;
    donation_url: string;
    ban: Ban,
    game_ban: Ban,
    blocked_by_me?: { silent: boolean }
    blocked_me: boolean,
    highest_role_order?: number,
    followed?: { notify: boolean }
}

export interface UserForm extends User {
    password: string,
    confirm_password: string,
    avatar_file?: Blob,
    banner_file?: Blob

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
    created_at?: string;
    updated_at?: string;
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
    created_at?: string;
    updated_at?: string;
    user_id: number;
    notifiable?: any | null;
    context?: any | null;
    user?: User;
    from_user?: User;
}

export interface Thread {
    id: number;
    name: string;
    content: string;
    views: number;
    archived: boolean;
    archived_by_mod: boolean;
    bumped_at?: string;
    pinned_at?: string;
    forum_id: number;
    category_id?: number;
    user_id: number;
    created_at?: string;
    updated_at?: string;
    comments?: Comment[];
    comments_count?: number;
    user?: User;
    last_user?: User;
    forum?: Forum;
    category?: ForumCategory;
    tag_ids?: number[];
    tags?: Tag[];
    subscribed?: boolean;
}

export interface Forum {
    id: number;
    game: Game,
    game_id?: number;
    created_at?: string;
    updated_at?: string;
}

export interface ForumCategory {
    id: number;
    name: string;
    desc: string;
    forum_id: number;
    created_at?: string;
    updated_at?: string;
}

export interface Suspension {
    id: number;
    mod_id: number;
    mod_user_id?: number;
    reason: string;
    status: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface Settings {
    max_file_size: number,
    mod_storage_size: number;
    image_max_file_size: number;
    mod_max_image_count: number;
    discord_webhook: string;
}

export interface Document {
    id: number;
    name: string;
    url_name: string;
    desc: string;
    game_id: number;
    last_user_id?: number;
    last_user?: User;
    created_at?: string;
    updated_at?: string;
}

export interface UserCase {
    id: number;
    user_id: number;
    mod_user_id: number;
    warning: boolean;
    reason: string;
    expire_date: string;
    created_at?: string;
    updated_at?: string;
    pardon_reason?: string;
    pardoned: boolean;
    ban?: Ban;
    user?: User;
    mod_user?: User;
}

export interface Dependency {
    id: number;
    name?: string;
    url?: string;
    mod_id?: number;
    optional: boolean;
    dependable_type: string;
    dependable_id: number;
    order: number;
    created_at?: string;
    updated_at?: string;
    mod?: Mod
}

export interface InstructsTemplate {
    id: number;
    name: string;
    instructions: string;
    localized: boolean;
    game_id: number;
    created_at?: string;
    updated_at?: string;
    dependencies?: Dependency[];
    dependencies_count?: number | null;
}

export interface GameUserData {
    ban: Ban;
    role_ids: Array<number>;
    highest_role_order: number;
    readonly permissions?: Record<string, boolean>;
}