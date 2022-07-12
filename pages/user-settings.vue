<template>
    <page-block size="med">
        <content-block class="p-8">
            <a-form @submit="save" :model="user" :can-save="canSaveOverride" float-save-gui>
                <a-tabs side type="query">
                    <a-tab name="account" title="Account">
                        <flex column gap="4">
                            <a-input label="Username" v-model="user.name"/>
                            <a-input label="Email" v-if="user.email || isMe" :disabled="!isMe" v-model="user.email"/>
                            <template v-if="isMe">
                                <h3>Change Password</h3>
                                <flex>
                                    <a-input label="New Password" type="password" v-model="password"/>
                                    <a-input label="Confirm Password" type="password" v-model="confirmPassword"/>
                                </flex>
                            </template>
                            <a-select label="roles" desc="As a regular user, you may only set vanity roles" v-model="user.role_ids" placeholder="Select Roles" multiple :options="roles.data"/>
                        </flex>
                    </a-tab>
                    <a-tab name="profile" title="Profile">
                        <flex column gap="4">
                            <img-uploader label="Avatar" id="avatar" :src="user.avatar" :file.sync="avatarBlob">
                                <template #label="{ src }">
                                    <a-avatar size="large" :src="src"/>
                                    <a-avatar size="medium" :src="src"/>
                                    <a-avatar size="small" :src="src"/>
                                </template>
                            </img-uploader>
    
                            <img-uploader label="Banner" column gap="3" id="banner" :src="user.banner" :file.sync="bannerBlob">
                                <template #label="{ src }">
                                    <div class="w-full round user-banner" :style="{backgroundImage: `url(${src || 'http://localhost:8000/storage/default_banner.webp'})`}"/>
                                </template>
                            </img-uploader>
    
                            <div>
                                <a-input label="Private Profile" type="checkbox" v-model="user.private_profile"/>
                                <br>
                                <small>Ticking this on will privatize your profile. Only staff members will be able to view it.</small>
                            </div>
    
                            <a-input label="Custom Title" v-model="user.custom_title"/>
                            <a-input label="Custom Color" v-model="user.custom_color" type="color"/>
    
                            <md-editor rows="12" v-model="user.bio" label="Bio" desc="Tell about yourself to people visiting your profile"/>
                        </flex>
                    </a-tab>
                    <a-tab name="options" title="Options">
                        
                    </a-tab>
                </a-tabs>
            </a-form>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';
import { useStore } from '../store';
import { Role, User } from '../types/models';

definePageMeta({
    middleware: 'admins-only'
});

const store = useStore();

const isMe = ref(false);

const avatarBlob = ref(null);
const bannerBlob = ref(null);

const route = useRoute();

const password = ref('');
const confirmPassword = ref('');

const user = ref<User>(null);
const id = parseInt(route.params.id?.toString());
if (id && id !== store.user.id) {
    user.value = await useGet<User>(`users/${route.params.id}`);
}
else {
    user.value = clone(store.user);
    isMe.value = true;
}

const { data: roles } = await useFetchMany<Role>('/roles?only_assignable=1');

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

        // for (const [k, v] of Object.entries(user.value)) {
        //     if (Array.isArray(v)) {
        //         for (const arrVal of v) { //Why is this even needed?????
        //             formData.append(k + '[]', arrVal);
        //         }
        //     } else {
        //         formData.append(k, v);
        //     }
        // }

        const nextUser = await usePatch<User>(`users/${user.value.id}`, {
            ...user.value,
            password: password.value,
            confirm_password: password.value
        });

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