<template>
    <page-block size="med">
        <content-block class="p-8">
            <a-form :model="models" :can-save="canSaveOverride" float-save-gui @submit="save">
                <a-tabs side type="query">
                    <a-tab name="account" title="Account">
                        <flex column gap="4">
                            <a-input v-model="user.name" label="Username"/>
                            <a-input v-if="user.email || isMe" v-model="user.email" label="Email" :disabled="!isMe"/>
                            <template v-if="isMe">
                                <h3>Change Password</h3>
                                <flex>
                                    <a-input v-model="password" label="New Password" type="password"/>
                                    <a-input v-model="confirmPassword" label="Confirm Password" type="password"/>
                                </flex>
                            </template>
                            <a-select v-model="user.role_ids" label="roles" desc="As a regular user, you may only set vanity roles" placeholder="Select Roles" multiple :options="roles.data"/>
                        </flex>
                    </a-tab>
                    <a-tab name="profile" title="Profile">
                        <flex column gap="4">
                            <img-uploader v-model="avatarBlob" label="Avatar" :src="avatarUrl">
                                <template #label="{ src }">
                                    <a-avatar size="large" :src="src"/>
                                    <a-avatar size="medium" :src="src"/>
                                    <a-avatar size="small" :src="src"/>
                                </template>
                            </img-uploader>
    
                            <img-uploader v-model="bannerBlob" label="Banner" :src="bannerUrl">
                                <template #label="{ src }">
                                    <div class="w-full round banner" :style="{backgroundImage: `url(${src})`}"/>
                                </template>
                            </img-uploader>
    
                            <div>
                                <a-input v-model="user.private_profile" label="Private Profile" type="checkbox"/>
                                <br>
                                <small>Ticking this on will privatize your profile. Only staff members will be able to view it.</small>
                            </div>
    
                            <a-input v-model="user.custom_title" label="Custom Title"/>
                            <a-input v-model="user.custom_color" label="Custom Color" type="color"/>
    
                            <md-editor v-model="user.bio" rows="12" label="Bio" desc="Tell about yourself to people visiting your profile"/>
                        </flex>
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

const bannerUrl = computed(function() {
    if (user.value.banner) {
        return `users/banners/${user.value.banner}`;
    } else {
        return 'default_banner.webp';
    }
});

const avatarUrl = computed(function() {
    if (user.value.avatar) {
        return `users/avatars/${user.value.avatar}`;
    }
});

if (id && id !== store.user.id) {
    user.value = await useGet<User>(`users/${route.params.id}`);
}
else {
    user.value = clone(store.user);
    isMe.value = true;
}

//Reactive unwraps the refs allowing us to watch these for a-form.
const models = reactive({user, avatarBlob, bannerBlob, password, confirmPassword});

const { data: roles } = await useFetchMany<Role>('/roles?only_assignable=1');

const canSaveOverride = computed(() => !!avatarBlob.value || !!bannerBlob.value);

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
</script>