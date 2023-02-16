export type PageMeta = {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export class Paginator<T = unknown> {
    constructor(data: T[]|null=null, meta: PageMeta|null = null) {
        if (data) {
            this.data = data;
        }
        if (meta) {
            this.meta = meta;
        }
    }

    data: T[] = [];
    meta: PageMeta = {
        current_page: 1,
        last_page: 1,
        per_page: 0,
        total: 0
    };
}
