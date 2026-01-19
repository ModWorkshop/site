import { Paginator } from './paginator';
/**
 * This file is auto generated using 'php artisan typescript:generate'
 *
 * Changes to this file will be lost when the command is run again
 */

export interface Model {
	id: number;
	created_at?: string;
	updated_at?: string;
}

export interface Category {
	id: number;
	name: string;
	short_name: string | null;
	desc: string;
	hidden: boolean;
	grid: boolean;
	display_order: number;
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
	disable_mod_managers: boolean;
	readonly path?: any;
}

export interface Comment {
	id: number;
	commentable_type: string;
	commentable_id: number;
	user_id: number;
	content: string;
	pinned: boolean;
	mentions: User[];
	reply_to: number;
	created_at?: string;
	updated_at?: string;
	user?: User;
	commentable?: Mod | Thread;
	last_replies?: Comment[];
	replies_count?: number;
	replies?: Comment[];
	replying_comment?: Comment;
	subscribed?: boolean;
	parser_version?: number;
}

export interface SimpleFile {
	id: number;
	downloads: number;
	user_id?: number;
	mod_id?: number;
	mod?: Mod | null;
	user?: User;
	file: string;
	type: string;
	created_at?: string;
	updated_at?: string;
	size: number;
	display_order: number;
}

export interface File extends SimpleFile {
	download_url: string;
	name: string;
	version: string;
	desc: string;
	label: string;
	image_id: number | null;
	approved: boolean;
}

export interface Image extends SimpleFile {
	has_thumb: boolean;
	visible: boolean;
}

export interface ModMember extends User {
	level: 'collaborator' | 'maintainer' | 'contributor' | 'viewer';
	accepted: boolean;
}

export interface Mod {
	id: number;
	breadcrumb?: Array<Breadcrumb>;
	thumbnail_id?: number | null;
	banner_id?: number | null;
	category_id?: number;
	game_id: number;
	user_id: number;
	allowed_storage?: number;
	name: string;
	desc: string;
	short_desc: string;
	changelog: string;
	license: string;
	instructions: string;
	depends_on?: unknown;
	visibility: 'public' | 'private' | 'unlisted';
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
	download_id?: number | null;
	download_type?: 'link' | 'file' | null;
	user?: User;
	last_user?: User;
	category?: Category;
	game?: Game;
	thumbnail?: Image;
	banner?: Image;
	background?: Image;
	tags?: Array<Tag>;
	images?: Array<Image>;
	files?: Paginator<File>;
	links?: Paginator<Link>;
	comments?: Array<Comment>;
	download?: File | Link;
	tags_count?: number;
	images_count?: number;
	files_count?: number;
	comment_count?: number;
	members: Array<ModMember>;
	transfer_request?: TransferRequest;
	tag_ids?: number[];
	last_suspension?: Suspension;
	followed?: { notify: boolean };
	subscribed?: boolean;
	send_for_approval?: boolean;
	dependencies?: Dependency[];
	instructs_template_id?: number;
	instructs_template?: InstructsTemplate;
	links_count?: number;
	mod_managers?: ModManager[];
	disable_mod_managers: boolean;
	background_id?: number | null;
	background_opacity?: number;
	used_storage?: number;
	parser_version?: number;
	repo_url?: string;
	ignored?: boolean;
}

export interface Breadcrumb {
	id?: string | number;
	type?: string;
	attachToPrev?: string;
	to?: string;
	name?: string;
}

export interface Permission {
	id: number;
	name: string;
	created_at?: string;
	updated_at?: string;
	type?: 'game' | 'global';
	allow: boolean;
}

export interface Role {
	id: number;
	name: string;
	tag: string;
	desc: string;
	color?: string;
	order: number;
	created_at?: string;
	updated_at?: string;
	is_vanity: boolean;
	self_assignable: boolean;
	permissions?: Record<string, boolean>;
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
	forum?: Forum;
	path?: string;
	followed?: boolean;
	ignored?: boolean;
	mods_count?: number;
	roles?: Role[];
	user_data?: GameUserData;
	announcements?: Thread[];
	categories?: Category[];
	report_count?: number;
	waiting_count?: number;
	mod_managers?: ModManager[];
	mod_manager_ids?: number[];
	hidden_tag_ids?: number[];
	default_mod_manager_id?: number;
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
	mod_user: User;
	created_at: string;
	expire_date: string;
	updated_at: string;
	active: boolean;
	reason: string;
	case_id: number;
	can_appeal: boolean;
	user: User;
	case: UserCase;
}

export interface User {
	id: number;
	name: string;
	unique_name: string;
	email: string | null;
	pending_email?: string;
	email_verified_at?: string;
	api_key?: string;
	activated?: boolean;
	created_at?: string;
	updated_at?: string;
	avatar: string;
	roles?: Role[];
	roles_count?: number;
	role_ids?: number[];
	game_role_ids?: number[];
	custom_color: string;
	color?: string;
	tag?: string;
	readonly role_names?: Record<string, boolean>;
	readonly permissions?: Record<string, boolean>;
	banner: string;
	bio: string;
	private_profile: boolean;
	invisible: boolean;
	custom_title: string;
	last_online?: string;
	donation_url: string;
	ban?: Ban;
	game_ban?: Ban;
	blocked_by_me?: { silent: boolean };
	blocked_me?: boolean;
	highest_role_order?: number;
	game_highest_role_order?: number;
	followed?: { notify: boolean };
	show_tag: 'role' | 'supporter_or_role' | 'none';
	active_supporter?: Supporter;
	has_supporter_perks?: boolean;
	signable?: boolean;
	extra?: {
		default_mods_view: string;
		default_mods_sort: string;
		home_default_mods_sort: string;
		game_default_mods_sort: string;
		home_show_last_games: boolean;
		home_show_mods: boolean;
		home_show_threads: boolean;
		game_show_mods: boolean;
		game_show_threads: boolean;
		auto_subscribe_to_mod: boolean;
		auto_subscribe_to_thread: boolean;
		background?: string;
		background_opacity?: number;
		developer_mode?: boolean;
	};
	mods_count?: number;
	supporter?: Supporter;
	nitro_token?: string;
	avatar_has_thumb?: boolean;
}

export interface UserForm extends User {
	password: string;
	confirm_password: string;
	current_password: string;
	avatar_file?: Blob | null;
	banner_file?: Blob | null;
	background_file?: Blob | null;

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
	display_order: number;
	image_id?: number;
	created_at?: string;
	updated_at?: string;
	user: User;
}

export type Download = (File | Link) & { download_type: 'file' | 'link' };

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
	locked: boolean;
	locked_by_mod: boolean;
	closed: boolean;
	closed_by_mod: boolean;
	answer_comment_id?: number | null;
	answer_comment?: Comment;
	announce: boolean;
	announce_until?: string;
	bumped_at?: string;
	pinned_at?: string;
	forum_id: number;
	category_id?: number;
	user_id: number;
	created_at?: string;
	updated_at?: string;
	comments?: Comment[];
	comment_count?: number;
	user?: User;
	last_user?: User;
	forum?: Forum;
	category?: ForumCategory;
	tag_ids?: number[];
	tags?: Tag[];
	subscribed?: boolean;
	game_id?: number;
	game?: Game;
	edited_at?: string;
	thumbnail?: string;
	parser_version?: number;
}

export interface Forum {
	id: number;
	game: Game;
	game_id?: number;
	created_at?: string;
	updated_at?: string;
}

export interface RolePolicy {
	can_view: boolean;
	can_post: boolean;
}

export interface ForumCategory {
	id: number;
	name: string;
	desc: string;
	emoji: string;
	forum_id: number;
	created_at?: string;
	updated_at?: string;
	display_order: number;
	role_policies?: Record<number, RolePolicy>;
	game_role_policies?: Record<number, RolePolicy>;
	banned_can_post: boolean;
	is_private: boolean;
	private_threads: boolean;
	can_post?: boolean;
	hidden?: boolean;
	can_close_threads?: boolean;
	grid_mode?: boolean;
}

export interface Suspension {
	id: number;
	mod_id: number;
	mod_user_id?: number;
	mod: Mod;
	reason: string;
	status: boolean;
	created_at?: string;
	updated_at?: string;
}

export interface Settings {
	max_file_size: number;
	mod_storage_size: number;
	supporter_mod_storage_size: number;
	image_max_file_size: number;
	mod_max_image_count: number;
	discord_webhook: string;
	discord_suspension_webhook: string;
	discord_approval_webhook: string;
	news_forum_category: number;
	edit_comment_threshold: number;
	game_requests_forum_category: number;
}

export interface Document {
	id: number;
	name: string;
	url_name: string;
	desc: string;
	game_id?: number;
	last_user_id?: number;
	last_user?: User;
	created_at?: string;
	updated_at?: string;
	is_unlisted: boolean;
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
	active: boolean;
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
	dependable_type?: string;
	dependable_id?: number;
	offsite: boolean;
	order: number;
	created_at?: string;
	updated_at?: string;
	mod?: Mod;
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
	highest_role_order?: number;
	readonly permissions?: Record<string, boolean>;
}

export interface Supporter {
	id: number;
	user_id: number;
	expire_date?: string;
	expired: boolean;
	created_at?: string;
	updated_at?: string;
	user?: User;
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
	created_at?: string;
	updated_at?: string;
	user?: User | null;
	reportable?: { id: number; user_id?: number } | null;
	reported_user?: User;
}

export interface ModManager {
	id: number;
	game_id?: number;
	name: string;
	image?: string;
	download_url: string;
	site_url: string;
	updated_at?: string;
	created_at?: string;
	hidden: boolean;
}

export interface PendingFileResponse {
	id: number;
	url: string;
	headers: Record<string, string>;
}

export interface AuditLog extends Model {
	user_id: number;
	type: string;
	name?: string;
	data: Record<string, any>;
	user?: User;
	game?: Game;
	game_id?: number;
	auditable_type?: string;
	auditable_id?: string;
	auditable_name?: string;
	auditable?: Model;
	context_type?: string;
	context_id?: number;
	context_name?: string;
	context?: Model;
}
