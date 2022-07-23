<template>
    <a-img :src="avatar" :class="{'avatar': true, [`avatar-${size}`]: !!size}"/>
</template>
<script setup lang="ts">
const props = defineProps({
    src: String,
    size: {
        default: null,
        type:  String
    }
});

const avatar = computed(() => {
    if (props.src) {
        return (props.src.startsWith('http') || props.src.startsWith('data:image')) ? props.src : `users/avatars/${props.src}`;
    } else {
        return 'assets/nopreview.png';
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