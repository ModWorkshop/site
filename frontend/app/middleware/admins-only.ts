import { useStore } from '~/store';

export default defineNuxtRouteMiddleware(() => {
    const { $pinia, $i18n } = useNuxtApp();
    const { hasPermission } = useStore();
    const { user } = storeToRefs(useStore($pinia));
    
    if (!user.value || !hasPermission('admin')) {
        showError({ statusCode: 403, statusMessage: $i18n.t('error_403'), fatal: true});
    }
});