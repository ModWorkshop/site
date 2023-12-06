<template>
    <m-flex gap="2" column>
        <m-input v-model="user.name" :label="$t('display_name')" maxlength="30"/>

        <m-img-uploader
            v-model="user.avatar_file"
            :label="$t('avatar')"
            :desc="$t('user_avatar_desc', { size: imageSize })"
            :src="user.avatar"
            :max-file-size="settings?.image_max_file_size"
        >
            <template #label="{ src }">
                <m-avatar size="xl" :src="src"/>
                <m-avatar size="lg" :src="src"/>
                <m-avatar size="md" :src="src"/>
            </template>
        </m-img-uploader>

        <m-img-uploader 
            v-model="user.banner_file"
            :label="$t('banner')" :desc="$t('user_banner_desc', { size: imageSize })"
            :src="user.banner"
            :max-file-size="settings?.image_max_file_size"
        >
            <template #label="{ src }">
                <m-banner :src="src" url-prefix="users/images"/>
            </template>
        </m-img-uploader>

        <m-select v-model="user.show_tag" :options="showTagOptions" :label="$t('show_tag')" :desc="$t('show_tag_desc')"/>

        <m-input v-model="user.donation_url" :label="$t('donation')" :desc="$t('donation_desc')"/>
        <m-input v-model="user.custom_title" :label="$t('custom_title')"/>
        <m-input v-if="user.active_supporter" v-model="user.custom_color" :label="$t('custom_color')" :desc="$t('custom_color_desc')" type="color"/>
        <m-input v-model="user.private_profile" :label="$t('private_profile')" :desc="$t('private_profile_desc')" type="checkbox"/>
        <m-input v-model="user.invisible" :label="$t('invisible')" :desc="$t('invisible_desc')" type="checkbox"/>
        <md-editor v-model="user.bio" rows="12" :label="$t('bio')" :desc="$t('bio_desc')"/>
    </m-flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { UserForm } from '~~/types/models';
import { useStore } from '~~/store/index';

defineProps<{
    user: UserForm
}>();

const { t } = useI18n();
const { settings } = useStore();
const imageSize = computed(() => friendlySize(settings?.image_max_file_size ?? 0));

const showTagOptions = [
    { id: 'role', name: t('show_tag_role') },
    { id: 'supporter_or_role', name: t('show_tag_supporter_or_role') },
    { id: 'none', name: t('hide') },
];
</script>