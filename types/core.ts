// For some reason cannot import it

import type { Canceler } from "axios";
import type { SimpleFile } from "./models";

// https://github.com/nuxt/nuxt/blob/main/packages/nuxt/src/app/composables/asyncData.ts#L97
export interface AsyncDataExecuteOptions {
    _initial?: boolean;
    dedupe?: 'cancel' | 'defer';
}

export type UploadFile = SimpleFile & {
    name?: string,
    cancel?: Canceler,
    progress?: number,
    thumbnail?: string,
    has_thumb?: boolean,
    waiting?: boolean,
    actualFile?: File
}