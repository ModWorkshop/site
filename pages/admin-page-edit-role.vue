<template>
    <admin-page route="roles">
        <div class="mb-3">
            <a-button icon="arrow-left" to="/admin/roles">Back to Roles</a-button>
        </div>
        <a-form :model="role" @submit="save" :created="role.id != -1" float-save-gui>
            <flex column gap="3">
                <group label="Name">
                    <el-input v-model="role.name" maxlength="100" minlength="3"/>
                </group>
                <group label="Tag" desc="If this role is special (Example: Moderator) what tag should it show?">
                    <el-input v-model="role.tag" maxlength="100" minlength="3"/>
                </group>
                <group label="Color" desc="The color of the role">
                    <el-color-picker v-model="role.color"/>
                </group>
                <group label="Permissions">
                    <flex class="p-4" gap="1" column grow>
                        <flex gap="1" v-for="perm of permissions" :key="perm.key">
                            <span class="flex-grow">{{perm.name}}</span>
                            <a-button @click="setPermission(perm.id, true)" :disabled="getPermissionState(perm.id) === true">✔</a-button>
                            <a-button @click="setPermission(perm.id)" :disabled="getPermissionState(perm.id) === null">❓</a-button>
                            <a-button @click="setPermission(perm.id, false)" :disabled="getPermissionState(perm.id) === false">❌</a-button>
                        </flex>
                    </flex>
                </group>
            </flex>
        </a-form>
    </admin-page>
</template>

<script>
    export default {
        data: () => ({
            role: {
                id: -1,
                name: '',
                tag: '',
                color: null,
                permissions: {}
            },
            permissions: []
        }),
        methods: {
            async save() {
                if (this.role.id == -1) {
                    this.role = await this.$factory.create('roles', this.role);
                } else {
                    this.role = await this.$factory.update('roles', this.role.id, this.role);
                }
            },
            getPermissionState(id) {
                console.log(id);
                const perm = this.role.permissions[id];
                return perm ? perm.allow : null;
            },
            setPermission(id, allow=null) {
                console.log(id, allow);

                if (this.role.permissions[id]) {
                    if (allow !== null) {
                        this.role.permissions[id].allow = allow;
                    } else {
                        this.$delete(this.role.permissions, id);
                    }
                } else {
                    this.$set(this.role.permissions, id, { allow });
                }
            }
        },
        async asyncData({ error, $axios, params }) {
            const permissions = await $axios.get('/permissions').then(res => res.data);

            if (params.role == 'new') {
                return { permissions };
            } else {
                try {
                    const role = await $axios.get(`/roles/${params.role}`).then(res => res.data);
                    return { role, permissions };
                } catch (err) {
                    if (err.response.status == 404) {
                        error("Role doesn't exist");
                    } else {
                        error("Something went wrong");
                    }
                }
            }
        }
    };
</script>

<style scoped>

</style>