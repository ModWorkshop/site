type Category = {
    updated_at: string /* Date */ | null;
    disporder: number;
    parent_id: number | null;
    game_id: number | null;
    approval_only: any // NOT FOUND;
    last_date: string /* Date */;
    created_at: string /* Date */ | null;
    id: number;
    hidden: any // NOT FOUND;
    grid: any // NOT FOUND;
    name: string;
    short_name: string | null;
    desc: string;
    webhook_url: string;
    thumbnail: string;
    banner: string;
    buttons: string;
    path?: any;
    breadcrumb?: any;
}
