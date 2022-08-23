import { FetchError } from 'ohmyfetch';

const strings = {
    500: 'Server Error. Please report to an admin'
};

export default function(error: FetchError|true, errorStr: string|Record<number, string>) {
    if (error instanceof Error) {
        if (typeof(errorStr) == 'string') {
            showError(errorStr);
        } else if (typeof(errorStr) == 'object') {
            const code = error.response.status;
            showError({ statusCode: code, statusMessage: errorStr[code] || strings[code] || 'Unknown Error'});
        }
    } else if (error) {
        showError({ statusCode: 404, statusMessage: errorStr[404] || 'Err'});
    }
}