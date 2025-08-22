import type { Game } from '~/types/models';
import { useStore } from '~/store/index';
import { useI18n } from 'vue-i18n';

export default function (perm: string, game?: Game) {
	const { $pinia } = useNuxtApp();
	const { hasPermission } = useStore($pinia);
	const { t } = useI18n();

	if (!hasPermission(perm, game)) {
		throw createError({ statusCode: 403, statusMessage: t('page_error_403'), fatal: true });
	}
}
