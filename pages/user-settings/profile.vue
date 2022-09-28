<template>
    <flex column>
        <img-uploader v-model="user.avatar_file" label="Avatar" :src="user.avatar">
            <template #label="{ src }">
                <a-avatar size="xl" :src="src"/>
                <a-avatar size="lg" :src="src"/>
                <a-avatar size="md" :src="src"/>
            </template>
        </img-uploader>

        <img-uploader v-model="user.banner_file" label="Banner" :src="user.banner">
            <template #label="{ src }">
                <a-banner :src="src" url-prefix="users/banners"/>
            </template>
        </img-uploader>
        <a-input v-model="user.private_profile" label="Private Profile" type="checkbox" desc="Ticking this on will privatize your profile. Only staff members will be able to view it."/>
        <a-input v-model="user.custom_title" label="Custom Title"/>
        <a-input v-model="user.custom_color" label="Custom Color" type="color"/>
        <md-editor v-model="user.bio" rows="12" label="Bio" desc="Tell about yourself to people visiting your profile"/>
    </flex>
</template>

<script setup lang="ts">
import { UserForm } from '~~/types/models';

defineProps<{
    user: UserForm
}>();
</script>

<style scoped>

</style>