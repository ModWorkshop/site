<template>
    <div>
        <a-form :model="role" :created="role.id != -1" float-save-gui @submit="save">
            <flex column gap="3">
                <div>
                    <a-button icon="arrow-left" to="/admin/roles">Back to Roles</a-button>
                </div>
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
            </flex>
        </a-form>
    </div>
</template>

<script setup lang="ts">
import { Permission, Role } from '~~/types/models';
import clone from 'rfdc/default';
import { Ref } from 'vue';

const route = useRoute();
let role: Ref<Role>;

if (route.params.id) {
    const { data: fetchedRole, error } = await useFetchData<Role>(`roles/${route.params.id}/`);
    role = fetchedRole;
} else {
    role = ref(clone({
        id: -1,
        name: '',
        tag: '',
        desc: '',
        color: null,
        permissions: []
    }));
}

const { data: permissions } = await useFetchMany<Permission>('/permissions');

async function save() {
    if (role.value.id == -1) {
        role.value = await usePost('roles', role.value);
        window.location.replace(`/admin/roles/${role.value.id}`);
    } else {
        role.value = await usePatch(`roles/${role.value.id}`, role.value);
    }
}

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