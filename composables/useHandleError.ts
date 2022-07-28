import { FetchError } from 'ohmyfetch';
import { createError } from 'h3';

export default function(error: FetchError, errorStr: string|Record<number, string>) {
    if (error instanceof Error) {
        if (typeof(errorStr) == 'string') {
            throw createError(errorStr);
        } else if (typeof(errorStr) == 'object') {
            const code = error.response.status;
            throw createError({ statusCode: 404, statusMessage: errorStr[code] || 'Err', fatal: true});
        }
    }
}