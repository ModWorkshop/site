<template>
    <admin-page route="roles">
        <div class="mb-3">
            <a-button icon="arrow-left" to="/admin/roles">Back to Roles</a-button>
        </div>
        <a-form :model="role" @submit="save" :created="role.id != -1" float-save-gui>
            <flex column gap="3">
                <group label="Name">
                    <a-input v-model="role.name" maxlength="100" minlength="3"/>
                </group>
                <group label="Tag" desc="If this role is special (Example: Moderator) what tag should it show?">
                    <a-input v-model="role.tag" maxlength="100" minlength="3"/>
                </group>
                <group label="Color" desc="The color of the role">
                    <a-input type="color" v-model="role.color"/>
                </group>
                <group label="Permissions">
                    <flex class="p-4" gap="1" column grow>
                        <flex gap="1" v-for="perm of permissions" :key="perm.key">
                            <span class="flex-grow">{{perm.name}}</span>
                            <a-button icon="check" @click="setPermission(perm.id, true)" :disabled="getPermissionState(perm.id) === true"/>
                            <a-button icon="question" @click="setPermission(perm.id)" :disabled="getPermissionState(perm.id) === null"/>
                            <a-button icon="xmark" @click="setPermission(perm.id, false)" :disabled="getPermissionState(perm.id) === false"/>
                        </flex>
                    </flex>
                </group>
            </flex>
        </a-form>
    </admin-page>
</template>

<script setup>
definePageMeta({
    middleware: 'admins-only'
});

const route = useRoute();
const role = ref({
    id: -1,
    name: '',
    tag: '',
    color: null,
    permissions: {}
});

if (route.params.id) {
    const { data: fetchedRole, error } = await useAPIFetch(`roles/${route.params.id}/`);
    role.value = fetchedRole.value;

    if (error) {
        
    }
}

const permissions = await useGet('/permissions');

async function save() {
    if (role.value.id == -1) {
        role.value = await usePost('roles', this.role);
    } else {
        role.value = await usePatch(`roles/${this.role.id}`, this.role);
    }
}

function getPermissionState(id) {
    const perm = role.value.permissions[id];
    return perm ? perm.allow : null;
}

function setPermission(id, allow=null) {
    console.log(id, allow);

    if (role.value.permissions[id]) {
        if (allow !== null) {
            role.value.permissions[id].allow = allow;
        } else {
            delete role.value.permissions[id];
        }
    } else {
        role.value.permissions[id] = { allow };
    }
}
</script>