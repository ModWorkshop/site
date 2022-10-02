<template>
    <simple-resource-form v-model="role" :url="url" :redirect-to="`/admin/${url}`" :delete-button="role.id !== 1">
        <a-alert v-if="role.id == 1" color="warning">
            <span>
                This is a special role that <strong>everyone</strong> has. It cannot be deleted and it can only be edited by a super admin.
            </span>
        </a-alert>
        <a-alert v-if="role.is_vanity" title="Vanity Role ✨" desc="Permissions are disabled."/>
        <a-input v-model="role.name" label="Name" maxlength="100" minlength="3"/>
        <a-input v-model="role.tag" label="Tag" :desc="$t('tag_help')" maxlength="100" minlength="3"/>
        <a-input v-model="role.color" label="Color" desc="The color of the role" type="color"/>
        <a-input v-if="!role.id" v-model="role.is_vanity" label="Vanity" desc="Vanity roles can be applied by anyone, but the can't have permissions." type="checkbox"/>
        <a-input v-if="!role.is_vanity" label="Permissions">
            <flex style="background-color: var(--alt-content-bg-color)" column grow>
                <flex 
                    v-for="perm of validPerms"
                    :key="perm.id"
                    class="perm items-center p-2"
                    :title="hasPermission(perm.name) ? undefined : `Cannot grant or deny permissions you don't have.`"
                >
                    <a-input
                        :model-value="role.permissions[perm.name]"
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

const gameId = route.params.gameId;
const url = computed(() => gameId ? `games/${gameId}/roles` : 'roles');

const { data: role } = await useEditResource<Role>('role', url.value, {
    id: 0,
    name: '',
    tag: '',
    desc: '',
    color: null,
    order: 1,
    is_vanity: false,
    permissions: {}
});

const { data: roles } = await useFetchMany<Role>('roles', { params: { with_permissions: 1 } });

const member = computed(() => roles.value.data[0]);

const { data: permissions } = await useFetchMany<Permission>('/permissions');

const validPerms = computed(() => {
    if (!gameId && role.value.id === 1) {
        return permissions.value.data;
    } else {
        return permissions.value.data.filter(perm => {
            const useless = member.value.permissions[perm.name];
            if (useless) {
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
    if (role.value.permissions[perm]) {
        delete role.value.permissions[perm];
    } else {
        role.value.permissions[perm] = true;
    }
}
</script>

<style>
.perm:nth-child(odd) {
    background-color: var(--input-bg-color);
}
</style>