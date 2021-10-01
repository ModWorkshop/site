<template>
    <flex :gap="gap" column :class="{'page-block': true, 'page-block-full': size == 'full', 'page-block-med': size == 'med'}">
        <slot/>
    </flex>
</template>

<script setup>
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

.page-block-med {
    width: 75%;
}
</style>