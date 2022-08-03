import { FetchError } from 'ohmyfetch';

export default function(error: FetchError, errorStr: string|Record<number, string>) {
    if (error instanceof Error) {
        if (typeof(errorStr) == 'string') {
            showError(errorStr);
        } else if (typeof(errorStr) == 'object') {
            const code = error.response.status;
            showError({ statusCode: 404, statusMessage: errorStr[code] || 'Err'});
        }
    }
}