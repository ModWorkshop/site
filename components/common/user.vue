<template>
    <flex inline :gap="avatar ? 2 : null">
        <nuxt-link v-if="avatar" :to="`/user/${user.id}`">
            <avatar :size="avatarSize" :src="src"/>
        </nuxt-link>
        <div class="inline-block">
            <nuxt-link :to="`/user/${user.id}`">
                {{user.name}}
            </nuxt-link>
            <template v-if="details">
                <br>
                {{details}}
            </template>
        </div>
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
    },
    computed: {
        src() {
            return `http://localhost:8000/storage/${this.user.avatar}`;
        }
    }
}
</script>