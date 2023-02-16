import { i18n } from './../app/i18n';
import { useStore } from '../store';
// Moves users from pages that are for guests only (login & register generally)

export default defineNuxtRouteMiddleware((to, from) => {
    const { $pinia } = useNuxtApp();
    const { user } = useStore($pinia);

    if (!user) {
        showError({ statusCode: 401, statusMessage: i18n.global.t('error_401'), fatal: true});
    } else if (user.ban) {
        showError({ statusCode: 403, statusMessage: i18n.global.t('error_403_banned'), fatal: true});
    } else if (!user.activated) {
        showError({ statusCode: 403, statusMessage: i18n.global.t('error_403_unactivated'), fatal: true});
    }
});