import { reloadToken } from '../utils/helpers';
async function check(force = false) {
	console.log('Checking for CSRF token...');
	if (force || !useCookie('XSRF-TOKEN', { readonly: true }).value) {
		console.log('Fetching CSRF token. ');
		reloadToken();
	}
}

export default defineNuxtPlugin(() => {
	if (import.meta.client) {
		// Set XSRF token for SSR
		setInterval(check, 300000); // Check every 5 minutes
		check(true);
	}
});
