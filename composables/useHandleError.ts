import { Ref } from 'vue';
import { FetchError } from 'ohmyfetch';

export default function(error: FetchError|true|Ref<FetchError|true>, errorStrings: string|Record<number|string, string> = {}) {
    error = unref(error);

    if (error instanceof Error) {
        if (typeof(errorStrings) == 'string') {
            throw createError(errorStrings);
        } else if (typeof(errorStrings) == 'object') {
            const code = error.response.status;
            throw createError({ statusCode: code, statusMessage: errorStrings[error.data.message] || errorStrings[code] || 'Unknown Error'});
        }
    } else if (error) {
        throw createError({ statusCode: 404, statusMessage: errorStrings[404] || 'Err', fatal: true});
    }
}