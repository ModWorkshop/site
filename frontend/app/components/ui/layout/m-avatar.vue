<template>
    <m-img
        :alt="$t('avatar')"
        loading="lazy"
        :src="avatarUrl"
        :use-thumb="useThumb"
        :url-prefix="src ? urlPrefix : undefined"
        :class="{'avatar': true, [`avatar-${size}`]: !!size}"
        :fallback="fallback"
        :width="sizeNumber"
        :height="sizeNumber"
    />
</template>
<script setup lang="ts">
const { public: config } = useRuntimeConfig();

const { src, size, urlPrefix = 'users/images' } = defineProps<{
    src?: string|Blob,
    urlPrefix?: string,
    size?: string,
    useThumb?: boolean
}>();

const assetsUrl = `${config.siteUrl}/assets`;

const fallback = computed(() => {
    if (!src && sizeNumber.value <= 48) {
        return assetsUrl + '/default-avatar-md.webp';
    }

    return assetsUrl + '/default-avatar.webp'
});
const avatarUrl = computed(() => src ?? fallback.value);

const sizes = {
    xs: 28,
    sm: 36,
    md: 48,
    lg: 64,
    xl: 150,
    '2xl': 200
};
const sizeNumber = computed(() => size ? sizes[size] : 36);
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
    width: 100px;
    height: 100px;
}

.avatar-2xl {
    width: 150px;
    height: 150px;
}

.avatar-3xl {
    width: 200px;
    height: 200px;
}
</style>