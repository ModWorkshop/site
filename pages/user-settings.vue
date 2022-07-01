<template>
    <page-block size="med">
        <content-block class="p-8">
            <a-form @submit="save" :model="user" style="flex: 1;" :can-save="canSaveOverride" float-save-gui>
                <a-tabs side>
                    <a-tab name="account" title="Account">
                        <group label="Username">
                            <a-input v-model="user.name"/>
                        </group>
                        <group label="Email" v-if="user.email || isMe">
                            <a-input :disabled="!isMe" v-model="user.email"/>
                        </group>
                        <template v-if="isMe">
                            <h3>Change Password</h3>
                            <group label="New Password">
                                <a-input type="password" v-model="user.password"/>
                            </group>
                            <group label="Confirm Password">
                                <a-input type="password" v-model="user.confirm_password"/>
                            </group>
                        </template>
                        <group label="roles" desc="As a regular user, you may only set vanity roles">
                            <a-select v-model="user.role_ids" placeholder="Select Roles" multiple :options="roles.data"/>
                        </group>
                    </a-tab>
                    <a-tab name="profile" title="Profile">
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
                                <a-input type="checkbox" v-model="user.private_profile">Private Profile</a-input>
                                <br>
                                <small>Ticking this on will privatize your profile. Only staff members will be able to view it.</small>
                            </div>
                        </group>

                        <group label="Custom Title">
                            <a-input v-model="user.custom_title"/>
                        </group>

                        <group label="Bio" desc="Tell about yourself to people visiting your profile">
                            <md-editor rows="12" v-model="user.bio"/>
                        </group>
                    </a-tab>
                    <a-tab name="options" title="Options">
                        
                    </a-tab>
                </a-tabs>
            </a-form>
        </content-block>
    </page-block>
</template>

<script setup>
import clone from 'rfdc/default';
import { useStore } from '../store';

definePageMeta({
    middleware: 'admins-only'
});

const store = useStore();

const isMe = ref(false);

const avatarBlob = ref(null);
const bannerBlob = ref(null);

const route = useRoute();

let user;
const id = parseInt(route.params.id);
if (id && id !== store.user.id) {
    const { data } = await useGet(`users/${route.params.id}`);
    user = data;
}
else {
    user = ref(clone(store.user));
    isMe.value = true;
}

const { data: roles } = await useAPIFetch('/roles?only_assignable=1');

user.password = '';
user.confirm_password = '';

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

        const nextUser = await usePatch(`users/${user.value.id}`, user.value);

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