<template>
    <m-flex gap="6" column>
        <m-img-uploader
            v-model="user.avatar_file"
            :label="$t('avatar')"
            :desc="$t('user_avatar_desc', { size: imageSize })"
            :src="user.avatar"
            :max-file-size="settings?.image_max_file_size"
        >
            <template #image="{ src }">
                <m-avatar size="xl" :src="src"/>
                <m-avatar size="lg" :src="src"/>
                <m-avatar size="md" :src="src"/>
            </template>
        </m-img-uploader>
        <m-flex flex>
            <m-input v-model="user.name" :label="$t('display_name')" maxlength="30"/>
            <m-input v-model="user.custom_color" :disabled="!user.has_supporter_perks" :label="true" :desc="$t('custom_color_desc')" type="color">
                <template #label>
                    {{$t('custom_color')}} <NuxtLink to="/support">{{$t('supporters_only')}}</NuxtLink>
                </template>
            </m-input>
        </m-flex>
        <md-editor v-model="user.bio" rows="12" :label="$t('bio')" :desc="$t('bio_desc')"/>

        <m-img-uploader 
            v-model="user.banner_file"
            :label="$t('banner')" :desc="$t('user_banner_desc', { size: imageSize })"
            :src="user.banner"
            :max-file-size="settings?.image_max_file_size"
        >
            <template #image="{ src }">
                <m-banner :src="src" url-prefix="users/images"/>
            </template>
        </m-img-uploader>

        <m-img-uploader 
            v-model="user.background_file"
            :label="true"
            :src="user.extra!.background"
            :max-file-size="settings?.image_max_file_size"
            :disabled="!user.has_supporter_perks"
        >
            <template #label>
                {{$t('supporter_background')}} <NuxtLink to="/support">{{$t('supporters_only')}}</NuxtLink>
            </template>
            <template #image="{ src }">
                <m-banner :src="src" url-prefix="users/images"/>
            </template>
        </m-img-uploader>
        <m-input 
            v-model="user.extra!.background_opacity"
            :disabled="!user.has_supporter_perks"
            :label="true"
            type="range"
            step="0.01"
            min="0"
            max="1"
        >
            <template #label>
                {{$t('supporter_background_opacity')}} <NuxtLink to="/support">{{$t('supporters_only')}}</NuxtLink>
            </template>
        </m-input>


        <m-select v-model="user.show_tag" :options="showTagOptions" :label="$t('show_tag')" :desc="$t('show_tag_desc')"/>
        <m-flex>
            <m-input v-model="user.donation_url" :label="$t('donation')" :desc="$t('donation_desc')"/>
            <m-input v-model="user.custom_title" :label="$t('custom_title')"/>
        </m-flex>
        <flex>
            <m-input v-model="user.private_profile" :label="$t('private_profile')" :desc="$t('private_profile_desc')" type="checkbox"/>
            <m-input v-model="user.invisible" :label="$t('invisible')" :desc="$t('invisible_desc')" type="checkbox"/>
        </flex>
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