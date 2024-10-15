// For some reason cannot import it
// https://github.com/nuxt/nuxt/blob/main/packages/nuxt/src/app/composables/asyncData.ts#L97
export interface AsyncDataExecuteOptions {
    _initial?: boolean;
    dedupe?: 'cancel' | 'defer';
}