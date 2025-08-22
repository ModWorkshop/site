import { FetchError } from 'ofetch';
import { H3Error } from 'h3';

export default function (error: unknown | Error | Ref<Error | null> | null, errorStrings: string | Record<number | string, string> = {}) {
	const err = unref(error);

	if (err instanceof Error) {
		if (err instanceof H3Error || err instanceof FetchError) {
			const code = err.statusCode;
			throw createError({
				statusCode: code,
				statusMessage: errorStrings[err.data.message] ?? (code ? errorStrings[code] : 'Unknown Error'),
				fatal: true
			});
		} else {
			throw createError({ statusCode: 418, statusMessage: 'Something went wrong, please report this error.' });
		}
	} else if (err) {
		throw createError({ statusCode: 404, statusMessage: errorStrings[404] || 'Err', fatal: true });
	}
}
