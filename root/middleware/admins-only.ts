import { useStore } from '../store';

export default defineNuxtRouteMiddleware(() => {
    const { $pinia, $i18n } = useNuxtApp();
    const { user, hasPermission } = useStore($pinia);
    
    if (!user || !hasPermission('admin')) {
        showError({ statusCode: 403, statusMessage: $i18n.t('error_403'), fatal: true});
    }
});