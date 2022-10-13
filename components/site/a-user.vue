<template>
    <flex inline :gap="neededGap">
        <NuxtLink v-if="avatar" :to="link">
            <a-avatar :size="avatarSize" :src="user.avatar" :style="{ opacity: isBanned ? 0.6 : 1 }"/>
        </NuxtLink>

        <flex gap="1" column class="my-auto">
            <VMenu 
                v-model:shown="renderProfile"
                :delay="{ show: 500, hide: 100 }"
                :auto-hide="false"
                :disabled="!miniProfile || static"
            >
                <NuxtLink class="flex gap-1 items-center" :to="link">
                    <component :is="isBanned ? 's' : 'span'" :style="{color: userColor}">{{user.name}}</component>
                    <a-tag v-if="userTag" small>{{userTag}}</a-tag>
                    <span v-if="showAt" class="user-at">@{{user.unique_name}}</span>
                    <slot name="after-name" :user="user"/>
                </NuxtLink>
                <template #popper>
                    <a-mini-profile v-click-outside="() => renderProfile = false" :user="user"/>
                </template>
            </VMenu>
            <slot name="details" :user="user">
                <span v-if="details">{{details}}</span>
            </slot>
        </flex>
        <slot name="attach"/>
    </flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { User } from '~~/types/models';

const props = withDefaults(defineProps<{
    details?: string,
    noColor?: boolean,
    user: User,
    miniProfile?: boolean,
    avatar?: boolean,
    tag?: boolean,
    avatarSize?: string,
    showAt?: boolean,
    static?: boolean,
}>(), { 
    avatar: true,
    tag: true,
    noColor: false,
    miniProfile: true
});

const { t } = useI18n();

const renderProfile = ref(false);
const isBanned = computed(() => !!(props.user.ban || props.user.game_ban));
const userTag = computed(() => {
    if (props.user.show_tag !== 'none') {
        if (props.user.show_tag === 'supporter_or_role' && props.user.supporter) {
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
        if (!props.noColor && props.user.color) {
            color = props.user.color.replace(/\s+/, '');
        }
    
        return color || 'var(--text-color)';
    }
});

const neededGap = computed(() => {
    if (props.avatar) {
        return props.avatarSize == 'xs' ? 1 : 2;
    } else {
        return null;
    }
});

const link = computed(() => {
    if (!props.static) {
        if (props.user.unique_name) {
            return `/user/${props.user.unique_name}`;
        } else {
            return `/user/${props.user.id}`;
        }
    }
});

function toggleRenderProfile() {
    if (!props.static) {
        renderProfile.value = true;
    }
}
</script>

<style>
.user-at {
    color: var(--text-inactive);
}
</style>