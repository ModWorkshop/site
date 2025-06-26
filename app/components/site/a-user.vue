<template>
    <m-dropdown
        v-model:open="renderProfile"
        :tool-tip-delay="500"
        class="user"
        type="tooltip"
        :disabled="!showMiniProfile || static"
    >
        <m-flex inline :column="column" class="items-center" :gap="neededGap">
            <NuxtLink v-if="avatar" class="inline-flex" :to="link">
                <m-avatar :size="avatarSize" :src="user?.avatar" :use-thumb="user?.avatar_has_thumb" :style="{ opacity: isBanned ? 0.6 : 1 }"/>
            </NuxtLink>

            <m-flex gap="1" class="break-words" column>
                <NuxtLink v-if="name" class="flex gap-1 items-center flex-wrap" :to="link">
                    <component :is="isBanned ? 's' : 'span'" :style="{color: userColor}" class="break-all text-body">{{user?.name ?? $t('invalid_user')}}</component>
                    <m-tag v-if="tag && userTag" small>{{userTag}}</m-tag>
                    <span v-if="showAt && user?.unique_name" class="user-at">@{{user?.unique_name}}</span>
                    <div v-if="showOnlineState && !userInvisible && isPublic" :title="statusString" class="circle" :style="{backgroundColor: statusColor}"/>

                    <slot name="after-name" :user="user"/>
                </NuxtLink>
                <small v-if="title">{{ user?.custom_title }}</small>
                <slot name="details" :user="user">
                    <span v-if="details">{{details}}</span>
                </slot>
            </m-flex>
            <slot name="attach"/>
        </m-flex>
        <template #content>
            <mini-profile v-if="user" :user="user"/>
        </template>
    </m-dropdown>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { User } from '~/types/models';
import { useStore } from '~/store';
import { differenceInMinutes, parseISO } from 'date-fns';

const props = withDefaults(defineProps<{
    details?: string,
    noColor?: boolean,
    user?: User|null,
    name?: boolean,
    showMiniProfile?: boolean,
    avatar?: boolean,
    tag?: boolean,
    avatarSize?: string,
    showAt?: boolean,
    static?: boolean,
    column?: boolean,
    title?: boolean,
    showOnlineState?: boolean,
}>(), { 
    avatar: true,
    tag: true,
    name: true,
    noColor: false,
    showMiniProfile: true,
    showOnlineState: false
});

const { t } = useI18n();
const { hasPermission, user: me } = useStore();

const renderProfile = ref(false);
const isOnline = computed(() => {
    if (!props.user?.last_online) {
        return false;
    }
    return differenceInMinutes(new Date(), parseISO(props.user.last_online)) < 10;
});
const userInvisible = computed(() => props.user?.invisible);
const isPublic = computed(() => !props.user?.private_profile || isOwnOrModerator.value);
const canModerateUsers = computed(() => hasPermission('moderate-users'));
const isOwnOrModerator = computed(() => me && (props.user?.id === me.id || canModerateUsers.value));
const statusString = computed(() => t(isOnline.value ? 'online' : 'offline'));
const statusColor = computed(() => isOnline.value ? 'green' : 'gray');

const isBanned = computed(() => !!(props.user?.ban || props.user?.game_ban));
const userTag = computed(() => {
    if (props.user && props.user.show_tag !== 'none') {
        if (props.user.show_tag === 'supporter_or_role' && props.user.active_supporter) {
            return t('supporter_tag');
        } else {
            return props.user.tag;
        }
    }
});

const userColor = computed(() => {
    if (isBanned.value) {
        return 'var(--secondary-color)';
    } else {
        let color;
        if (!props.noColor && props.user?.color && props.user.color.replace) {
            color = props.user.color.replace(/\s+/, '');
        }
    
        return color || 'var(--text-color)';
    }
});

const neededGap = computed(() => {
    if (props.avatar) {
        return props.avatarSize == 'xs' ? 1 : 2;
    }
});

const link = computed(() => {
    if (!props.static && props.user) {
        if (props.user.unique_name) {
            return `/user/${props.user.unique_name}`;
        } else {
            return `/user/${props.user.id}`;
        }
    }
});
</script>

<style scoped>
.user {
    display: inline-flex;
}
</style>

<style>
.user-at {
    color: var(--text-inactive);
}

.user > div {
    width: 100%;
}
</style>