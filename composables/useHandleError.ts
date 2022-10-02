import { FetchError } from 'ohmyfetch';
import { Ref } from 'vue';

function isFetchError(error): error is FetchError {
    return !!error.response;
}

export default function(error: FetchError|Error|Ref<true | FetchError | Error>, errorStrings: string|Record<number|string, string> = {}) {
    const err = unref(error);

    if (err instanceof Error) {
        if (typeof(errorStrings) == 'string') {
            throw createError(errorStrings);
        } else if (isFetchError(err)) {
            const code = err.response.status;
            throw createError({ statusCode: code, statusMessage: errorStrings[err.data.message] || errorStrings[code] || 'Unknown Error'});
        } else {
            throw createError({ statusCode: 418, statusMessage: 'Uhh does this ever hit?'});
        }
    } else if (err) {
        throw createError({ statusCode: 404, statusMessage: errorStrings[404] || 'Err', fatal: true});
    }
}