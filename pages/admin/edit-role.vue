<template>
    <simple-resource-form v-model="role" url="roles" redirect-to="/admin/roles" :delete-button="role.id !== 1">
        <a-alert v-if="role.id == 1" color="warning">
            <span>
                This is a special role that <strong>everyone</strong> has, it cannot be deleted.
                Be careful with what permissions you give it.
            </span>
        </a-alert>
        <a-input v-model="role.name" label="Name" maxlength="100" minlength="3"/>
        <a-input v-model="role.tag" label="Tag" :desc="$t('tag_help')" maxlength="100" minlength="3"/>
        <a-input v-model="role.color" label="Color" desc="The color of the role" type="color"/>
        <a-input label="Permissions">
            <flex style="background-color: var(--alt-content-bg-color)" column grow>
                <flex v-for="perm of permissions.data" :key="perm.id" class="perm p-2">
                    <a-input 
                        :model-value="role.permissions[perm.id]"
                        class="p-3"
                        :label="perm.name"
                        type="checkbox"
                        @update:model-value="togglePermission(perm.id)"
                    />
                </flex>
            </flex>
        </a-input>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { Permission, Role } from '~~/types/models';
import clone from 'rfdc/default';

const { data: role } = await useEditResource<Role>('role', 'roles', clone({
    id: 0,
    name: '',
    tag: '',
    desc: '',
    color: null,
    permissions: []
}));

const { data: permissions } = await useFetchMany<Permission>('/permissions');

function togglePermission(permId: number) {
    if (role.value.permissions[permId]) {
        delete role.value.permissions[permId];
    } else {
        role.value.permissions[permId] = true;
    }
}
</script>

<style>
.perm:nth-child(odd) {
    background-color: var(--input-bg-color);
}
</style>