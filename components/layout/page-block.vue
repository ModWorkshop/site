<template>
    <flex :gap="gap" column :class="classes">
        <slot/>
    </flex>
</template>

<script setup lang="ts">
const props = defineProps({
    gap: {
        type: Number,
        default: 3
    },
    size: {
        type: String,
        default: 'large'
    }
});

const classes = computed(() => ({
    'page-block': true,
    'h-full': true,
    'page-block-full': props.size == 'full',
    'page-block-md': props.size == 'md',
    'page-block-sm': props.size == 'sm'
}));
</script>

<style scoped>
.page-block {
    border-radius: 4px;
    width: 83%;
}

.page-block-full {
    width: 100%;
}

.page-block-md {
    width: 75%;
}

.page-block-sm {
    width: 70%;
}
</style>