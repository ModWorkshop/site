<template>
    <flex inline :gap="neededGap">
        <NuxtLink v-if="avatar" :to="link">
            <a-avatar :size="avatarSize" :src="user.avatar" :style="{ opacity: isBanned ? 0.6 : 1 }"/>
        </NuxtLink>
        <flex gap="1" column class="my-auto">
            <NuxtLink class="flex gap-1 items-center" :to="link">
                <component :is="isBanned ? 's' : 'span'" :style="{color: userColor}">{{user.name}}</component>
                <a-tag v-if="tag && user.tag" small>{{user.tag}}</a-tag>
                <span v-if="showAt" class="user-at">@{{user.unique_name}}</span>
                <slot name="after-name" :user="user"/>
            </NuxtLink>
            <slot name="details" :user="user">
                <span v-if="details">{{details}}</span>
            </slot>
        </flex>
        <slot name="attach"/>
    </flex>
</template>

<script setup lang="ts">
import { User } from '~~/types/models';

const props = withDefaults(defineProps<{
    details?: string,
    color?: boolean,
    user: User,
    avatar?: boolean,
    tag?: boolean,
    avatarSize?: string,
    showAt?: boolean,
    static?: boolean,
}>(), { 
    avatar: true,
    tag: true,
    color: true
});

const isBanned = computed(() => !!(props.user.ban || props.user.game_ban));

const userColor = computed(() => {
    if (isBanned.value) {
        return 'var(--secondary-color)';
    } else {
        let color;
        if (props.color && props.user.color) {
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

const link = computed(() => !props.static && `/user/${props.user.id}` || null);
</script>

<style>
.user-at {
    color: var(--text-inactive);
}
</style>