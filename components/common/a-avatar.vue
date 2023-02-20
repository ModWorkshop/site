<template>
    <a-img
        :alt="$t('avatar')"
        :src="avatarUrl"
        :url-prefix="src ? urlPrefix : undefined"
        :class="{'avatar': true, [`avatar-${size}`]: !!size}"
        :width="sizeNumber"
        :height="sizeNumber"
    />
</template>
<script setup lang="ts">
const { public: config } = useRuntimeConfig();

const props = defineProps({
    src: [String, Blob],
    urlPrefix: {
        type: String,
        default: 'users/avatars'
    },
    size: {
        default: null,
        type:  String
    }
});
const assetsUrl = `${config.apiUrl}/assets`;

const avatarUrl = computed(() => {
    const src = props.src;
    if (config.debug_legacy_images && !isSrcExternal(src)) {
        return 'https://modworkshop.net/' + src;
    }
    else if (src) {
        return src;
    } else {
        return `${assetsUrl}/default-avatar.webp`;
    }
});

const sizes = {
    xs: 28,
    sm: 36,
    md: 48,
    lg: 64,
    xl: 150,
    '2xl': 200
};
const sizeNumber = computed(() => sizes[props.size] ?? 36);
</script>
<style scoped>
.avatar {
    display: inline-block;
    object-fit: cover;
    width: 36px;
    height: 36px;
    border-radius: 10%;
}

.avatar-xs {
    width: 28px;
    height: 28px;
}

.avatar-sm {
    width: 36px;
    height: 36px;
}

.avatar-md {
    width: 48px;
    height: 48px;
}

.avatar-lg {
    width: 64px;
    height: 64px;
}

.avatar-xl {
    width: 150px;
    height: 150px;
}

.avatar-2xl {
    width: 200px;
    height: 200px;
}
</style>