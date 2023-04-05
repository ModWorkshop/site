<template>
    <a-tag-selector
        v-model="user.role_ids"
        multiple
        url="roles"
        :fetch-params="{ only_assignable: 1 }"
        :label="$t('roles')"
        :disabled="user.id !== me?.id && !hasPermission('manage-roles')"
        :enabled-by="role => role.assignable"
        :color-by="item => item.color"
        :before-select="askAreYouSure"
        @update:model-value="prepareSaveRoles"
    />
    <a-tag-selector 
        v-if="currentGame"
        v-model="user.game_role_ids"
        :url="`games/${currentGame?.id}/roles`"
        :fetch-params="{ only_assignable: 1 }"
        multiple
        :disabled="user.id !== me?.id && !hasPermission('manage-roles', currentGame)"
        :label="$t('game_roles')"
        :enabled-by="role => role.assignable"
        :color-by="item => item.color"
        :before-select="askAreYouSure"
        @update:model-value="prepareSaveGameRoles"
    />
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { User, Role } from '../../types/models';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    user: User
}>();

const { currentGame, hasPermission, user: me } = useStore();
const yesNoModal = useYesNoModal();
const { t } = useI18n();
const { start: prepareSaveGameRoles } = useTimeoutFn(saveGameRoles, 500, { immediate: false });
async function saveGameRoles() {
    await usePatch(`games/${currentGame!.id}/users/${props.user.id}/roles`, { role_ids: props.user.game_role_ids });
}

const { start: prepareSaveRoles } = useTimeoutFn(saveRoles, 500, { immediate: false });
async function saveRoles() {
    await usePatch(`users/${props.user.id}/roles`, { role_ids: props.user.role_ids });
}

function askAreYouSure(value: Role, clbk) {
    console.log(value);
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