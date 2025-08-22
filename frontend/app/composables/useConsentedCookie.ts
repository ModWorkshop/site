import type { CookieOptions } from '#app';

/**
 * Consent-aware useCookie. Only necessary if you need to set otherwise you can freely use the regular useCookie.
 * If cookies are not allowed sends a useState instead.
 */
export default function<T>(name: string, _opts: undefined|(CookieOptions<T> & { readonly?: false }) = undefined) {
	const allowCookies = useCookie('allow-cookies');

	if (Boolean(allowCookies.value) === true) {
		return useCookie<T>(name, _opts);
	} else {
		return useState<T>(name, _opts?.default);
	}
}
