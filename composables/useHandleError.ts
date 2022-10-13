import { useStore } from './../store/index';
import { FetchError } from 'ohmyfetch';
import { Ref } from 'vue';

function isFetchError(error): error is FetchError {
    return !!error.response;
}

export default function(error: FetchError|Error|Ref<true | FetchError | Error>, errorStrings: string|Record<number|string, string> = {}) {
    const err = unref(error);

    const store = useStore();

    if (err instanceof Error) {
        if (isFetchError(err)) {
            const code = err.response.status;
            throw createError({ 
                statusCode: code,
                statusMessage: errorStrings[err.data.message] || errorStrings[code] || 'Unknown Error',
                fatal: true,
                data: JSON.stringify({ user: store.user, theme: store.theme, scheme: store.colorScheme })
            });
        } else {
            throw createError({ statusCode: 418, statusMessage: 'Uhh does this ever hit?'});
        }
    } else if (err) {
        throw createError({ statusCode: 404, statusMessage: errorStrings[404] || 'Err', fatal: true});
    }
}