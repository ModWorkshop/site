<template>
    <page-block size="med">
        <content-block class="p-8">
            <a-form @submit="save" :model="user" style="flex: 1;" :can-save="canSaveOverride" float-save-gui>
                <tabs side>
                    <form-tab name="account" title="Account">
                        <group label="Username">
                            <el-input v-model="user.name"/>
                        </group>
                        <group label="Email">
                            <el-input v-model="user.email"/>
                        </group>
                        <h3>Change Password</h3>
                        <group label="New Password">
                            <el-input type="password" v-model="user.password"/>
                        </group>
                        <group label="Confirm Password">
                            <el-input type="password" v-model="user.confirm_password"/>
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
    import { useStore, useContext, computed, ref } from '@nuxtjs/composition-api';
    import { Notification } from 'element-ui';
    import clone from 'rfdc/default';

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

            const { $axios } = useContext();
            const user = ref({});
            const avatar = ref();
            let avatarBolb = $ref(null);
            let bannerBolb = $ref(null);

            const canSaveOverride = computed(() => avatarBolb != null || bannerBolb != null);
            const currentAvatarSrc = computed(() => avatarBolb || user.value.avatar);

            function onAvatarChosen() {
                const file = avatar.value.files[0];
                const reader = new FileReader(file);
                reader.onload = () => {
                    avatarBolb = reader.result;
                };
                reader.readAsDataURL(file);
            }

            if (store.getters.user) {
                const nextUser = clone(store.getters.user);
                nextUser.password = '';
                nextUser.confirm_password = '';
                user.value = nextUser;
            }

            async function save() {
                try {
                    const formData = new FormData();
                    if (avatar.value.files.length > 0) {
                        formData.append('avatar-file', avatar.value.files[0]);
                        avatar.value = '';
                        avatarBolb = null;
                        bannerBolb = null;
                    }
                    
                    for (const [k, v] of Object.entries(user.value)) {
                        formData.append(k, v);
                    }

                    const nextUser = await $axios.patch(`users/${user.value.id}`, formData).then(res => res.data);
                    
                    nextUser.password = '';
                    nextUser.confirm_password = '';

                    store.commit('setUser', clone(nextUser));

                    user.value = nextUser;
                } catch (error) {
                    console.log(error);
                    new Notification.error('Failed saving user settings');
                }
            }

            return { avatar, user, canSaveOverride, currentAvatarSrc, onAvatarChosen, save };
        }
    };
</script>