<template>
    <flex inline :gap="neededGap">
        <NuxtLink v-if="avatar" :to="link">
            <a-avatar :size="avatarSize" :src="user.avatar"/>
        </NuxtLink>
        <flex gap="1" column class="my-auto">
            <NuxtLink class="flex gap-1 items-center" :to="link" :style="{color: user.color}">
                <span>{{user.name}}</span>
                <a-tag v-if="tag && user.tag" small color="#2169ff">{{user.tag}}</a-tag>
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
    user: User,
    avatar?: boolean,
    tag?: boolean,
    avatarSize?: string,
    showAt?: boolean,
    static?: boolean,
}>(), { avatar: true, tag: true });

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