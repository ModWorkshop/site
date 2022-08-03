<template>
    <flex :gap="gap" column :class="{'page-block': true, 'page-block-full': size == 'full', 'page-block-md': size == 'md', 'page-block-sm': size == 'sm'}">
        <slot/>
    </flex>
</template>

<script setup lang="ts">
defineProps({
    gap: {
        type: Number,
        default: 3
    },
    size: {
        type: String,
        default: 'large'
    }
});

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