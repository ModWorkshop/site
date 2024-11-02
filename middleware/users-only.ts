import { useStore } from '../store';
// Moves users from pages that are for guests only (login & register generally)

export default defineNuxtRouteMiddleware(() => {
    const { $pinia, $i18n } = useNuxtApp();
    const { user } = storeToRefs(useStore($pinia));

    if (!user.value) {
        showError({ statusCode: 401, statusMessage: $i18n.t('page_error_401'), fatal: true});
    }
});