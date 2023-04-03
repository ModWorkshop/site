<template>
    <simple-resource-form v-if="role" v-model="role" :url="url" :redirect-to="adminUrl" :delete-button="role.id !== 1">
        <a-alert v-if="role.id == 1" color="warning">
            <span>{{$t('members_role_desc')}}</span>
        </a-alert>
        <a-alert v-if="role.id && role.is_vanity" :title="`${$t('vanity_role')}✨`" :desc="$t('vanity_role_desc')"/>
        <a-input v-model="role.name" :label="$t('name')" maxlength="100" minlength="3"/>
        <a-input v-model="role.tag" :label="$t('user_tag')" :desc="$t('user_tag_help')" maxlength="100" minlength="3"/>
        <a-input v-model="role.color" :label="$t('color')" type="color"/>
        <a-input v-if="!role.id" v-model="role.is_vanity" type="checkbox" :label="$t('vanity_role')" :desc="$t('vanity_role_desc')"/>
        <a-input v-if="!role.is_vanity" :label="$t('permissions')">
            <flex style="background-color: var(--alt-content-bg-color)" column grow>
                <flex 
                    v-for="perm of validPerms"
                    :key="perm.id"
                    class="perm items-center p-2"
                    :title="hasPermission(perm.name) ? undefined : $t('cant_grant_permission')"
                >
                    <a-input
                        v-model="role.permissions![perm.name]"
                        class="p-3"
                        :label="perm.name"
                        type="checkbox"
                        :disabled="!hasPermission(perm.name)"
                        @update:model-value="togglePermission(perm.name)"
                    />
                    <span v-if="!hasPermission(perm.name)">🔒</span>
                </flex>
            </flex>
        </a-input>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Game, Permission, Role } from '~~/types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-roles', props.game);

const route = useRoute();
const { hasPermission } = useStore();

const gameId = route.params.game;
const url = computed(() => gameId ? `games/${gameId}/roles` : 'roles');
const adminUrl = computed(() => getAdminUrl('roles', props.game));

const { data: role } = await useEditResource<Role>('role', url.value, {
    id: 0,
    name: '',
    tag: '',
    desc: '',
    order: 1,
    is_vanity: false,
    permissions: {}
});

const { data: roles } = await useFetchMany<Role>('roles', { params: { with_permissions: 1 } });

const member = computed(() => roles.value?.data[0]);

const { data: permissions } = await useFetchMany<Permission>('/permissions');

const validPerms = computed(() => {
    if (!gameId && role.value.id === 1) {
        return permissions.value?.data;
    } else {
        return permissions.value?.data.filter(perm => {
            const useless = member.value?.permissions?.[perm.name];
            if (useless) { // Everyone has this permission already!
                return false;
            }

            if (perm.type) {
                return gameId ? perm.type === 'game' : perm.type === 'global';
            } else {
                return true;
            }
        });
    }
});

function togglePermission(perm: string) {
    if (role.value.permissions) {
        if (role.value.permissions[perm]) {
            delete role.value.permissions[perm];
        } else {
            role.value.permissions[perm] = true;
        }
    }
}
</script>

<style>
.perm:nth-child(odd) {
    background-color: var(--input-bg-color);
}
</style>