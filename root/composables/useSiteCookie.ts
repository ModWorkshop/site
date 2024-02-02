import type { CookieOptions, CookieRef } from "#app";

export default function<T=string | null | undefined>(name: string, _opts?: (CookieOptions<T> & { readonly?: boolean; }) | undefined) {
    const { public: runtimeConfig } = useRuntimeConfig();

    if (_opts && _opts.readonly) {
        return useCookie<T>(name, {
            domain: runtimeConfig.cookieUrl as string,
            ..._opts,
            readonly: true,
        }) as CookieRef<T>; //TODO: figure out how to make typescript realize that this if statement doesn't always run
    } else {
        return useCookie<T>(name, {
            domain: runtimeConfig.cookieUrl as string,
            ..._opts,
            readonly: false,
        }) as CookieRef<T>;
    }
}