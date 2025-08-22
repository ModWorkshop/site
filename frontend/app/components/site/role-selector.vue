<template>
	<m-select
		v-model="roleIds"
		multiple
		url="roles"
		:label="$t('roles')"
		:disabled="user.id !== me?.id && !hasPermission('manage-roles')"
		:enabled-by="role => role.assignable"
		:color-by="item => item.color"
		:before-select="askAreYouSure"
		:classic="false"
		:clearable="false"
		list-tags
		@update:model-value="prepareSaveRoles"
	/>
	<m-select
		v-if="currentGame"
		v-model="gameRoleIds"
		:url="`games/${currentGame?.id}/roles`"
		multiple
		:disabled="user.id !== me?.id && !hasPermission('manage-roles', currentGame)"
		:label="$t('game_roles')"
		:enabled-by="role => role.assignable"
		:color-by="item => item.color"
		:before-select="askAreYouSure"
		:classic="false"
		:clearable="false"
		list-tags
		@update:model-value="prepareSaveGameRoles"
	/>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';

import { useStore } from '~/store';
import type { User, Role } from '~/types/models';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
	user: User;
}>();

const { currentGame, hasPermission, user: me } = useStore();
const yesNoModal = useYesNoModal();
const { t } = useI18n();
const { start: prepareSaveGameRoles } = useTimeoutFn(saveGameRoles, 500, { immediate: false });
const showError = useQuickErrorToast();

const roleIds = reactive(props.user.role_ids ?? []);
const gameRoleIds = reactive(props.user.game_role_ids ?? []);

let prevGameRoles, prevRoles;

onMounted(() => {
	prevGameRoles = clone(props.user.game_role_ids);
	prevRoles = clone(props.user.role_ids);
});

async function saveGameRoles() {
	try {
		await patchRequest(`games/${currentGame!.id}/users/${props.user.id}/roles`, { role_ids: props.user.game_role_ids });
		prevGameRoles = clone(props.user.game_role_ids);
	} catch (error) {
		showError(error);
		props.user.game_role_ids = clone(prevGameRoles);
	}
}

const { start: prepareSaveRoles } = useTimeoutFn(saveRoles, 500, { immediate: false });
async function saveRoles() {
	try {
		await patchRequest(`users/${props.user.id}/roles`, { role_ids: props.user.role_ids });
		prevRoles = clone(props.user.role_ids);
	} catch (error) {
		showError(error);
		props.user.role_ids = clone(prevRoles);
	}
}

function askAreYouSure(value: Role, clbk) {
	if (!value.is_vanity) {
		yesNoModal({
			desc: t('are_you_sure_role'),
			yes: clbk
		});
	} else {
		clbk();
	}
}

</script>
