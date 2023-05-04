<template>
    <flex ref="flexRef" :gap="gap" :padding="padding" :column="column" :class="{'content-block': true, 'alt-content-bg': altBackground}">
        <slot/>
    </flex>
</template>

<script setup lang="ts">
defineProps({
    gap: {
        type: [Number, String],
        default: 3
    },
    column: {
        type: Boolean,
        default: true
    },
    padding: {
        type: [Number, String, Boolean],
        default: 4
    },
    altBackground: Boolean
});

const flexRef = ref();

const flexElement = computed(() => flexRef.value.element);

defineExpose({ 
    element: flexElement
});
</script>