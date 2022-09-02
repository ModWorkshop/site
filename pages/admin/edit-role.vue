<template>
    <simple-resource-form v-model="role" url="roles" redirect-to="/admin/roles" :delete-button="role.id !== 1">
        <a-alert v-if="role.id == 1" color="warning">
            This is a special role that <strong>everyone</strong> has, it cannot be deleted.
            Be careful with what permissions you give it.
        </a-alert>
        <a-input v-model="role.name" label="Name" maxlength="100" minlength="3"/>
        <a-input v-model="role.tag" label="Tag" :desc="$t('tag_help')" maxlength="100" minlength="3"/>
        <a-input v-model="role.color" label="Color" desc="The color of the role" type="color"/>
        <a-input label="Permissions">
            <flex class="p-4" style="background-color: #22262a" column grow>
                <flex v-for="perm of permissions.data" :key="perm.id" class="perm p-2">
                    <span class="flex-grow my-auto">{{perm.name}}</span>
                    <a-button icon="check" :disabled="getPermissionState(perm.id) === true" @click="setPermission(perm, true)"/>
                    <a-button icon="question" :disabled="getPermissionState(perm.id) === undefined" @click="setPermission(perm)"/>
                    <a-button icon="xmark" :disabled="getPermissionState(perm.id) === false" @click="setPermission(perm, false)"/>
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

function getPermissionState(id: number) {
    return role.value.permissions[id];
}

function setPermission(perm: Permission, allow=null) {
    const id = perm.id;

    if (allow !== null) {
        role.value.permissions[id] = allow;
    } else if (Object.hasOwn(role.value.permissions, id)) {
        delete role.value.permissions[id];
    }
}
</script>

<style>
.perm:nth-child(odd) {
    background-color: var(--input-bg-color);
}
</style>