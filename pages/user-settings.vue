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
                            <img-uploader id="avatar" :src="user.avatar" :file.sync="avatarBlob">
                                <template #label="{ src }">
                                    <a-avatar size="large" :src="src"/>
                                    <a-avatar size="medium" :src="src"/>
                                    <a-avatar size="small" :src="src"/>
                                </template>
                            </img-uploader>
                        </group>

                        <group label="Banner" column gap="3">
                            <img-uploader id="banner" :src="user.banner" :file.sync="bannerBlob">
                                <template #label="{ src }">
                                    <div class="w-full round user-banner" :style="{backgroundImage: `url(${src || 'http://localhost:8000/storage/default_banner.webp'})`}"/>
                                </template>
                            </img-uploader>
                        </group>

                        <group>
                            <div>
                                <el-checkbox v-model="user.private_profile">Private Profile</el-checkbox>
                                <br>
                                <small>Ticking this on will privatize your profile. Only staff members will be able to view it.</small>
                            </div>
                        </group>

                        <group label="Custom Title">
                            <el-input v-model="user.custom_title"/>
                        </group>

                        <group label="Bio" desc="Tell about yourself to people visiting your profile">
                            <md-editor rows="12" v-model="user.bio"/>
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
import { useStore } from '../store';

export default {
    middleware({ $pinia, error }) {
        const store = useStore($pinia);
        if (!store.user) {
            error({
                statusCode: 401,
                message: 'You must be logged in to enter this page!'
            });
        }
    }
};
</script>

<script setup>
import clone from 'rfdc/default';

const store = useStore();

const user = ref({
    name: '',
    role_ids: [],
    bio: '',
    private_profile: false
});

const isMe = ref(false);
const roles = ref([]);

const avatarBlob = ref(null);
const bannerBlob = ref(null);

const route = useRoute();

useFetch(async () => {
    let nextUser;
    const id = parseInt(route.value.params.id);
    if (id && id !== store.user.id) {
        nextUser = await $ftch.get(`users/${route.value.params.id}`);
    }
    else {
        nextUser = clone(store.user);
        isMe.value = true;
    }

    const rolesRes = await $ftch.get('/roles?only_assignable=1');
    roles.value = rolesRes.data;

    nextUser.password = '';
    nextUser.confirm_password = '';
    user.value = nextUser;
});

const canSaveOverride = computed(() => !!avatarBlob.value || !!bannerBlob.value);

async function save() {
    try {
        const formData = new FormData();
        if (avatarBlob.value) {
            formData.append('avatar_file', avatarBlob.value);
            avatarBlob.value = null;
        }
        if (bannerBlob.value) {
            formData.append('banner_file', bannerBlob.value);
            bannerBlob.value = null;
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

        const nextUser = await $ftch.patch(`users/${user.value.id}`, user.value);

        nextUser.password = '';
        nextUser.confirm_password = '';

        if (isMe.value) {
            store.user = clone(nextUser);
        }

        user.value = nextUser;
    } catch (error) {
        console.log(error);
        // Notification.error('Failed saving user settings');
    }
}
</script>