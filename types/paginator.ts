export class Paginator<T = unknown> {
    data: Array<T>;
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: number;
        per_page: number;
        to: number;
        total: number;
    }
}
