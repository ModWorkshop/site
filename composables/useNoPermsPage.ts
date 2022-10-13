import { useI18n } from "vue-i18n";

export default function() {
    const { t } = useI18n();
    showError({statusMessage: t('error_403'), statusCode: 403, fatal: true});
}