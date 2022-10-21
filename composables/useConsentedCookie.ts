import { CookieOptions } from "nuxt/dist/app/composables";

/**
 * Consent-aware useCookie. Only necessary if you need to set otherwise you can freely use the regular useCookie.
 * If cookies are not allowed sends a useState instead.
 */
export default function<T>(name: string, _opts?: CookieOptions<T>) {
    const allowCookies = useCookie('allow-cookies');

    if (Boolean(allowCookies.value) === true) {
        return useCookie(name, _opts);
    } else {
        return useState<T>(name, _opts?.default);
    }
}   
