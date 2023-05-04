<template>
    <flex gap="2" column>
        <img-uploader v-model="user.avatar_file" :label="$t('avatar')" :src="user.avatar">
            <template #label="{ src }">
                <a-avatar size="xl" :src="src"/>
                <a-avatar size="lg" :src="src"/>
                <a-avatar size="md" :src="src"/>
            </template>
        </img-uploader>

        <img-uploader v-model="user.banner_file" :label="$t('banner')" :src="user.banner">
            <template #label="{ src }">
                <a-banner :src="src" url-prefix="users/images"/>
            </template>
        </img-uploader>

        <a-select v-model="user.show_tag" :options="showTagOptions" :label="$t('show_tag')" :desc="$t('show_tag_desc')"/>

        <a-input v-model="user.donation_url" :label="$t('donation')" :desc="$t('donation_desc')"/>
        <a-input v-model="user.custom_title" :label="$t('custom_title')"/>
        <a-input v-if="user.active_supporter" v-model="user.custom_color" :label="$t('custom_color')" :desc="$t('custom_color_desc')" type="color"/>
        <a-input v-model="user.private_profile" :label="$t('private_profile')" :desc="$t('private_profile_desc')" type="checkbox"/>
        <a-input v-model="user.invisible" :label="$t('invisible')" :desc="$t('invisible_desc')" type="checkbox"/>
        <md-editor v-model="user.bio" rows="12" :label="$t('bio')" :desc="$t('bio_desc')"/>
    </flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { UserForm } from '~~/types/models';

defineProps<{
    user: UserForm
}>();

const { t } = useI18n();

const showTagOptions = [
    { id: 'role', name: t('show_tag_role') },
    { id: 'supporter_or_role', name: t('show_tag_supporter_or_role') },
    { id: 'none', name: t('hide') },
];
</script>