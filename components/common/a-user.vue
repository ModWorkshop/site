<template>
    <flex inline :gap="avatar ? 2 : null">
        <nuxt-link v-if="avatar" :to="`/user/${user.id}`">
            <a-avatar :size="avatarSize" :src="user.avatar"/>
        </nuxt-link>
        <flex gap="1" column class="my-auto">
            <nuxt-link :to="`/user/${user.id}`" :style="{color: user.color}">
                {{user.name}}<a-tag small v-if="user.tag" color="#2169ff">{{user.tag}}</a-tag>
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
    avatarSize?: string
}>(), { avatar: true });
</script>