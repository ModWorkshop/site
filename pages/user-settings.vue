<template>
    <page-block v-if="user" size="md">
        <content-block class="p-8">
            <a-form :model="user" :can-save="canSaveOverride" float-save-gui @submit="save">
                <a-tabs side type="query">
                    <a-tab name="account" title="Account">
                            <a-input v-model="user.name" label="Username"/>
                            <a-input v-model="user.unique_name" label="Unique Name" desc="A unique name for your profile and to allow people to mention you."/>
                            <a-input v-model="user.donation_url" label="Donation Link" desc="Supports PayPal, Ko-Fi, and Buy Me a Coffee. Shows in your profile and mod pages."/>
                            <a-input v-if="user.email || isMe" v-model="user.email" label="Email" :disabled="!isMe"/>
                            <template v-if="isMe">
                                <h3>Change Password</h3>
                                <flex>
                                    <a-input v-model="password" label="New Password" type="password"/>
                                    <a-input v-model="confirmPassword" label="Confirm Password" type="password"/>
                                </flex>
                            </template>
                            <a-select v-model="user.role_ids" label="roles" desc="As a regular user, you may only set vanity roles" placeholder="Select Roles" multiple :options="roles.data"/>
                            <a-alert class="w-full" color="warning">
                                <details>
                                    <summary class="uppercase">{{$t('danger_zone')}}</summary>
                                    <div class="p-4 mt-2">
                                        <a-button color="danger" @click="doDelete">{{$t('delete')}}</a-button>
                                    </div>
                                </details>
                            </a-alert>
                    </a-tab>
                    <a-tab name="profile" title="Profile">
                            <img-uploader v-model="avatarBlob" label="Avatar" :src="user.avatar">
                                <template #label="{ src }">
                                    <a-avatar size="xl" :src="src"/>
                                    <a-avatar size="lg" :src="src"/>
                                    <a-avatar size="md" :src="src"/>
                                </template>
                            </img-uploader>
    
                            <img-uploader v-model="bannerBlob" label="Banner" :src="user.banner">
                                <template #label="{ src }">
                                    <a-banner :src="src" url-prefix="users/banners"/>
                                </template>
                            </img-uploader>
                            <a-input v-model="user.private_profile" label="Private Profile" type="checkbox" desc="Ticking this on will privatize your profile. Only staff members will be able to view it."/>
                            <a-input v-model="user.custom_title" label="Custom Title"/>
                            <a-input v-model="user.custom_color" label="Custom Color" type="color"/>
                            <md-editor v-model="user.bio" rows="12" label="Bio" desc="Tell about yourself to people visiting your profile"/>
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
    middleware: 'users-only'
});

const store = useStore();

const isMe = ref(false);

const avatarBlob = ref(null);
const bannerBlob = ref(null);

const route = useRoute();
const router = useRouter();

const password = ref('');
const confirmPassword = ref('');

const { data: user } = await useResource<User>('user', 'users', clone(store.user));
isMe.value = !route.params.userId;

const { data: roles } = await useFetchMany<Role>('/roles?only_assignable=1');

const canSaveOverride = computed(() => !!(avatarBlob.value || bannerBlob.value || password.value || confirmPassword.value));

async function save() {
    try {
        const nextUser = await usePatch<User>(`users/${user.value.id}`, serializeObject({
            ...user.value,
            password: password.value,
            confirm_password: password.value,
            avatar_file: avatarBlob.value,
            banner_file: bannerBlob.value
        }));

        //Clear the image upload after success
        avatarBlob.value = null;
        bannerBlob.value = null;

        if (isMe.value) {
            store.user = clone(nextUser);
        }

        user.value = nextUser;
    } catch (error) {
        console.log(error);
    }
}

async function doDelete() {
    try {
        await useDelete(`users/${user.value.id}`);
        await store.logout();
    } catch (error) {
        console.log(error);
                
    }
}
</script>