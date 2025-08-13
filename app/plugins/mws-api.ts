export default defineNuxtPlugin((nuxtApp) => {
	const token = useCookie('XSRF-TOKEN', { readonly: true });
	const headers = useRequestHeaders();
	const { public: config, innerApiUrl } = useRuntimeConfig();

	const mwsAPI = $fetch.create({
		baseURL: import.meta.client ? config.apiUrl : innerApiUrl,
		// Use custom query serializer for PHP-style arrays
		query: {},
		credentials: 'include',
		onRequest({ request, options, error }) {
			// Handle custom query parameter formatting for GET requests
			if (options && options.params && (!options.method || options.method == 'GET')) {
				//This retarded code is brought you by stupid web standards https://blog.shalvah.me/posts/fun-stuff-representing-arrays-and-objects-in-query-strings
				//tl;dr - PHP and JS cannot agree on the format for arrays in queries, we shall use PHP's one.
				
				// Merge params into query and use custom serialization
				const customQuery = buildQueryParams(options.query, false);

				const query = customQuery.split('&').map(param => param.split('='));
				const newQuery = {}
				for (const [k,v] of query) {
					if (k) {
						newQuery[k] = v;
					}
				}
				 
				options.query = newQuery
			}
			options.headers.set('referer', config.siteUrl);

			if (headers.cookie) {				
				options.headers.set('cookie', headers.cookie);
			}
			
			if (token.value) {
				options.headers.set('X-XSRF-TOKEN', token.value);
			}

			options.headers.set('accept', 'application/json');

			// This *should* be safe! If we use Caddy to proxy, Caddy should ignore all of these (if give by user) and set it by itself
			if (import.meta.server) {
				if (headers['X-forwarded-proto']) {
					options.headers.set('x-forwarded-proto', headers['X-forwarded-proto']);
				}
				if (headers['x-forwarded-host']) {
					options.headers.set('x-forwarded-host', headers['x-forwarded-host']);
				}
				if (headers['x-forwarded-for']) {
					options.headers.set('x-forwarded-for', headers['x-forwarded-for']);
				}
				if (headers['x-forwarded-for']) {
					options.headers.set('x-forwarded-ip', headers['x-forwarded-for']);
				}
			}
		}
	})

	return {
		provide: {
			mwsAPI
		}
	}
})
