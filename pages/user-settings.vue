<template>
    <page-block size="med">
        <content-block class="p-8">
            <a-form @submit="save" :model="user" style="flex: 1;" :can-save="canSaveOverride" float-save-gui>
                <tabs side>
                    <form-tab name="account" title="Account">
                        <group label="Username">
                            <el-input v-model="user.name"/>
                        </group>
                        <group label="Email" v-if="user.email || isMe">
                            <el-input :disabled="!isMe" v-model="user.email"/>
                        </group>
                        <template v-if="isMe">
                            <h3>Change Password</h3>
                            <group label="New Password">
                                <el-input type="password" v-model="user.password"/>
                            </group>
                            <group label="Confirm Password">
                                <el-input type="password" v-model="user.confirm_password"/>
                            </group>
                        </template>
                        <group label="roles" desc="As a regular user, you may only set vanity roles">
                            <el-select v-model="user.role_ids" placeholder="Select Roles" multiple filterable>
                                <el-option v-for="role in roles" :key="role.id" :label="role.name" :value="role.id"/>
                            </el-select>
                        </group>
                    </form-tab>
                    <form-tab name="profile" title="Profile">
                        <group label="Avatar" column gap="3">
                            <label class="flex items-end gap-2" for="avatar-upload">
                                <a-avatar size="large" :src="currentAvatarSrc"/>
                                <a-avatar size="medium" :src="currentAvatarSrc"/>
                                <a-avatar size="small" :src="currentAvatarSrc"/>
                            </label>
                            <input ref="avatar" type="file" id="avatar-upload" @change="onAvatarChosen"/>
                        </group>
                    </form-tab>
                    <form-tab name="options" title="Options">
                        
                    </form-tab>
                </tabs>
            </a-form>
        </content-block>
    </page-block>
</template>

<script>
    import { useFetch, useRoute } from '@nuxtjs/composition-api';
    import { Notification } from 'element-ui';
    import clone from 'rfdc/default';
    import { useStore } from '../store';

    export default {
        middleware({ store, error }) {
            if (!store.state.user) {
                error({
                    statusCode: 401,
                    message: 'You must be logged in to enter this page!'
                });
            }
        },
        setup() {
            const store = useStore();
            const { $axios, $factory } = useNuxtApp().legacyApp;

            const user = ref({
                name: '',
                role_ids: []
            });
            const avatar = ref();
            const isMe = ref(false);
            const roles = ref([]);

            const avatarBolb = ref(null);
            const bannerBolb = ref(null);

            const route = useRoute();

            useFetch(async () => {
                let nextUser;
                const id = parseInt(route.value.params.id);
                if (id && id !== store.user.id) {
                    nextUser = await $factory.getOne('users', route.value.params.id);
                }
                else {
                    nextUser = clone(store.user);
                    isMe.value = true;
                }

                const rolesRes = await $axios.get('/roles?only_assignable=1').then(res => res.data);
                roles.value = rolesRes.data;

                nextUser.password = '';
                nextUser.confirm_password = '';
                user.value = nextUser;
            });

            const canSaveOverride = computed(() => avatarBolb.value != null || bannerBolb.value != null);
            const currentAvatarSrc = computed(() => avatarBolb.value || user.value.avatar);

            function onAvatarChosen() {
                const file = avatar.value.files[0];
                const reader = new FileReader(file);
                reader.onload = () => {
                    avatarBolb.value = reader.result;
                };
                reader.readAsDataURL(file);
            }

            async function save() {
                try {
                    const formData = new FormData();
                    if (avatar.value.files.length > 0) {
                        formData.append('avatar-file', avatar.value.files[0]);
                        avatar.value = '';
                        avatarBolb.value = null;
                        bannerBolb.value = null;
                    }

                    for (const [k, v] of Object.entries(user.value)) {
                        if (Array.isArray(v)) {
                            for (const arrVal of v) { //Why is this even needed?????
                                formData.append(k + '[]', arrVal);
                            }
                        } else {
                            formData.append(k, v);
                        }
                    }

                    const nextUser = await $axios.patch(`users/${user.value.id}`, formData).then(res => res.data);

                    nextUser.password = '';
                    nextUser.confirm_password = '';

                    if (isMe.value) {
                        store.user = clone(nextUser);
                    }

                    user.value = nextUser;
                } catch (error) {
                    console.log(error);
                    Notification.error('Failed saving user settings');
                }
            }

            return { avatar, user, canSaveOverride, currentAvatarSrc, onAvatarChosen, save, isMe, roles };
        }
    };
</script>