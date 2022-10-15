import { FetchError } from 'ohmyfetch';
import { useI18n } from 'vue-i18n';

export default function() {
    const { t } = useI18n();
    const { showToast } = useToaster();

    return function(e: FetchError, errorStrings: Record<number|string, string> = {}, title?: string) {
        errorStrings = {
            ...errorStrings
        };

        return showToast({
            color: 'danger',
            title,
            desc: errorStrings[e.data.message] || errorStrings[e.response.status] || t('something_went_wrong')
        });
    };
}