<template>
    <a-img :src="avatarUrl" :url-prefix="src ? urlPrefix : null" :class="{'avatar': true, [`avatar-${size}`]: !!size}"/>
</template>
<script setup lang="ts">
const props = defineProps({
    src: String,
    urlPrefix: {
        type: String,
        default: 'users/avatars/'
    },
    size: {
        default: null,
        type:  String
    }
});

const avatarUrl = computed(() => {
    const src = props.src;
    if (src) {
        return src;
    } else {
        return '/assets/nopreview.webp';
    }
});
</script>
<style scoped>
    .avatar {
        display: inline;
        object-fit: cover;
        width: 38px;
        height: 38px;
        border-radius: 10%;
    }

    .avatar-xs {
        width: 28px;
        height: 28px;
    }

    .avatar-small {
        width: 32px;
        height: 32px;
    }

    .avatar-medium {
        width: 48px;
        height: 48px;
    }

    .avatar-large {
        width: 150px;
        height: 150px;
    }
    
    .avatar-largest {
        width: 200px;
        height: 200px;
    }
</style>