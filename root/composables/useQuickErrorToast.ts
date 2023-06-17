import { AxiosError } from 'axios';
import { FetchError } from 'ofetch';
import { useI18n } from 'vue-i18n';

function getErrorString(e) {
    let i = 1;
            
    if (e) {
        if (e.data && e.data.message) {
            const data = e.data;

            if (data.errors) {
                let errStr = '';
                for (const err of Object.values(data.errors)) {
                    for (const str of (err as string[])) {
                        if (errStr) {
                            errStr += '\n' + i + '. ' + str;
                        } else {
                            errStr = i + '. ' + str;
                        }
                        i++;
                    }
                }
                return errStr;
            } else if (data.message) {
                return data.message;
            }
        } else {
            return e.message;
        }
    }
}

export default function() {
    const { t } = useI18n();
    const { showToast } = useToaster();

    return function(e: unknown|FetchError|AxiosError, errorStrings: Record<number|string, string> = {}, title?: string) {
        if (!(e instanceof FetchError || e instanceof AxiosError)) {
            return;
        }

        errorStrings = {
            409: t('error_409'),
            403: t('error_403'),
            404: t('error_404'),
            500: t('error_500'),
            502: t('error_502'),
            429: t('error_429'),
            ...errorStrings
        };

        const code = e.status ?? e.response?.status ?? 502;
        let message;
        if (e instanceof FetchError) {
            message = e.message ?? e.data?.message;
        } else {
            message = e.message;
        }

        let desc = '';
        if (code === 422) {
            desc = getErrorString(e);
        } else {
            desc = errorStrings[message] || errorStrings[code] || t('something_went_wrong');
        }

        desc += ` (${code})`;

        return showToast({
            color: 'danger',
            title,
            desc
        });
    };
}