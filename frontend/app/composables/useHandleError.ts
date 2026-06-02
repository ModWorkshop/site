import { FetchError } from 'ofetch';
import { H3Error } from 'h3';
import { AxiosError } from 'axios';
export default function () {
	const { t } = useI18n();

	return (error: unknown | Error | Ref<Error | null> | null, errorStrings: Record<number | string, string> = {}) => {
		const err = unref(error);

		if (!err) return;

		if (err instanceof Error) {
			errorStrings = {
				403: t('error_403'),
				404: t('error_404'),
				409: t('error_409'),
				500: t('error_500'),
				429: t('error_429'),
				502: t('error_502'),
				...errorStrings
			};

			let code: number | undefined;
			let message: string | undefined;

			if (err instanceof H3Error || err instanceof FetchError) {
				code = err.statusCode;
				message = err.data.message;
			} else if (err instanceof AxiosError) {
				code = err.status;
				message = err.response?.data.message;
			} else {
				throw createError({ statusCode: 418, statusMessage: 'Something went wrong, please report this error.' });
			}

			if (message) {
				message = errorStrings[message] ?? message;
			}

			throw createError({
				statusCode: code,
				statusMessage: code ? errorStrings[code] : 'Unknown Error',
				message,
				fatal: true
			});
		} else {
			throw createError({ statusCode: 404, statusMessage: errorStrings[404] || 'Err', fatal: true });
		}
	};
}
