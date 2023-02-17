import { FetchError } from 'ohmyfetch';
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

    return function(e: FetchError, errorStrings: Record<number|string, string> = {}, title?: string) {
        errorStrings = {
            409: t('error_409'),
            ...errorStrings
        };

        const code = e.status ?? e.response?.status ?? 419;
        const message = e.statusMessage ?? e.data?.message;
        let desc = '';
        if (code === 422) {
            desc = getErrorString(e);
        } else {
            desc = errorStrings[message] || errorStrings[code] || t('something_went_wrong');
        }

        return showToast({
            color: 'danger',
            title,
            desc
        });
    };
}