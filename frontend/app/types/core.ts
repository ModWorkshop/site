// For some reason cannot import it

import type { AxiosProgressEvent, Canceler } from 'axios';
import type { File as MWSFile, SimpleFile } from './models';

// https://github.com/nuxt/nuxt/blob/main/packages/nuxt/src/app/composables/asyncData.ts#L97
export interface AsyncDataExecuteOptions {
	_initial?: boolean;
	dedupe?: 'cancel' | 'defer';
}

export type UploadSimpleFile = SimpleFile & {
	name?: string;
	cancel?: Canceler;
	progress?: AxiosProgressEvent;
	thumbnail?: string;
	has_thumb?: boolean;
	waiting?: boolean;
	actualFile?: File;
};

export type UploadFile = UploadSimpleFile & MWSFile;
