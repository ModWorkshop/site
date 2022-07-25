<template>
    <flex inline :gap="avatar ? 2 : null">
        <nuxt-link v-if="avatar" :to="`/user/${user.id}`">
            <a-avatar :size="avatarSize" :src="user.avatar"/>
        </nuxt-link>
        <flex gap="1" column class="my-auto">
            <nuxt-link :to="!static && `/user/${user.id}`" :style="{color: user.color}">
                {{user.name}}
                <a-tag v-if="user.tag" small color="#2169ff" class="mr-1">{{user.tag}}</a-tag>
                <span v-if="showAt" class="user-at">@{{user.unique_name}}</span>
            </nuxt-link>
            <template v-if="details">
                <span>{{details}}</span>
            </template>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { User } from '~~/types/models';

withDefaults(defineProps<{
    details?: string,
    user: User,
    avatar?: boolean,
    avatarSize?: string,
    showAt?: boolean,
    static?: boolean,
}>(), { avatar: true });
</script>

<style>
.user-at {
    color: var(--text-inactive);
}
</style>