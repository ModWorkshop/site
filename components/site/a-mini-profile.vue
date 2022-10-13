<template>
    <div>
        <Suspense>
            <flex style="width: 350px;">
                <content-block alt-background padding="5" grow>
                    <span class="text-xl">
                        <a-user :user="user" avatar-size="lg" :mini-profile="false"/>
                    </span>
                    <a-tag-selector
                        v-model="user.role_ids"
                        multiple
                        url="roles"
                        :fetch-params="{ only_assignable: 1 }"
                        :label="$t('roles')"
                        :disabled="user.id !== me?.id && !hasPermission('manage-roles')"
                        :enabled-by="role => role.assignable"
                        :color-by="item => item.color"
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
                        @update:model-value="prepareSaveGameRoles"
                    />
                </content-block>
            </flex>
        </Suspense>
    </div>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { User, Role } from '~~/types/models';

const props = defineProps<{
    user: User
}>();

const { currentGame, hasPermission, user: me } = useStore();

const { start: prepareSaveGameRoles } = useTimeoutFn(saveGameRoles, 500, { immediate: false });
async function saveGameRoles() {
    await usePatch(`games/${currentGame.id}/users/${props.user.id}/roles`, { role_ids: props.user.game_role_ids });
}

const { start: prepareSaveRoles } = useTimeoutFn(saveRoles, 500, { immediate: false });
async function saveRoles() {
    await usePatch(`users/${props.user.id}/roles`, { role_ids: props.user.role_ids });
}
</script>