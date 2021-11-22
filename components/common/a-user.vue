<template>
    <flex inline :gap="avatar ? 2 : null">
        <nuxt-link v-if="avatar" :to="`/user/${user.id}`">
            <a-avatar :size="avatarSize" :src="user.avatar"/>
        </nuxt-link>
        <flex inline>
            <nuxt-link :to="`/user/${user.id}`" :style="{color: user.color}" class="my-auto">
                {{user.name}} <a-tag small v-if="user.tag" color="#2169ff">{{user.tag}}</a-tag>
            </nuxt-link>
            <template v-if="details">
                <br>
                {{details}}
            </template>
        </flex>
    </flex>
</template>
<script>

export default {
    props: {
        details: String,
        user : Object,
        avatar: {
            default: true,
            type: Boolean
        },
        avatarSize: {
            default: null,
            type: String
        },
    }
};
</script>