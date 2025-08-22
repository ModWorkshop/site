import { useStore } from '~/store';
// Moves users from pages that are for guests only (login & register generally)

export default defineNuxtRouteMiddleware(() => {
	const { $pinia, $i18n } = useNuxtApp();
	const { user } = storeToRefs(useStore($pinia));

	if (!user.value) {
		showError({ statusCode: 401, statusMessage: $i18n.t('page_error_401'), fatal: true });
	} else if (user.value.ban) {
		showError({ statusCode: 403, statusMessage: $i18n.t('page_error_403_banned'), fatal: true });
	} else if (!user.value.activated) {
		showError({ statusCode: 403, statusMessage: $i18n.t('page_error_403_unactivated'), fatal: true });
	}
});
