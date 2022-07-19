<template>
    <div>
        <a-form :model="role" @submit="save" :created="role.id != -1" float-save-gui>
            <flex column gap="3">
                <div>
                    <a-button icon="arrow-left" to="/admin/roles">Back to Roles</a-button>
                </div>
                <a-input label="Name" v-model="role.name" maxlength="100" minlength="3"/>
                <a-input label="Tag" :desc="$t('tag_help')" v-model="role.tag" maxlength="100" minlength="3"/>
                <a-input label="Color" desc="The color of the role" type="color" v-model="role.color"/>
                <a-input label="Permissions">
                    <flex class="p-4" style="background-color: #22262a" column grow>
                        <flex v-for="perm of permissions.data" :key="perm.id" class="perm p-2">
                            <span class="flex-grow my-auto">{{perm.name}}</span>
                            <a-button icon="check" @click="setPermission(perm, true)" :disabled="getPermissionState(perm.id) === true"/>
                            <a-button icon="question" @click="setPermission(perm)" :disabled="getPermissionState(perm.id) === null"/>
                            <a-button icon="xmark" @click="setPermission(perm, false)" :disabled="getPermissionState(perm.id) === false"/>
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
    role = clone({
        id: -1,
        name: '',
        tag: '',
        desc: '',
        color: null,
        permissions: []
    });
}

const { data: permissions } = await useFetchMany<Permission>('/permissions');

async function save() {
    if (role.value.id == -1) {
        role.value = await usePost('roles', this.role);
        window.location.replace(`/admin/roles/${role.value.id}`);
    } else {
        role.value = await usePatch(`roles/${this.role.id}`, this.role);
    }
}

function getPermissionState(id) {
    const perm = role.value.permissions[id];
    return perm ? perm.allow : null;
}

function setPermission(perm: Permission, allow=null) {
    const id = perm.id;

    if (role.value.permissions[id]) {
        if (allow !== null) {
            role.value.permissions[id].allow = allow;
        } else {
            delete role.value.permissions[id];
        }
    } else {
        role.value.permissions[id] = { ...perm, allow };
    }
}
</script>

<style>
.perm:nth-child(odd) {
    background-color: var(--input-bg-color);
}
</style>