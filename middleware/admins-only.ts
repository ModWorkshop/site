import { i18n } from './../app/i18n';
import { useStore } from '../store';

export default defineNuxtRouteMiddleware(() => {
    const { $pinia } = useNuxtApp();
    const { user, hasPermission } = useStore($pinia);
    
    if (!user || !hasPermission('admin')) {
        showError({ statusCode: 403, statusMessage: i18n.global.t('error_403'), fatal: true});
    }
});