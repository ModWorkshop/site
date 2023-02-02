<template>
    <Icon :name="iconFullName" :size="size"/>
</template>

<script setup lang="ts">
const props = defineProps<{
    icon?: string, // TODO: deprecate
    name?: string|number,
    size?: string,
}>();

const iconName = computed(() => props.icon ?? props.name);

const iconFullName = computed(() => {
    if (iconName.value) {
        if (iconName.value.toString().includes(':')) {
            return iconName.value;
        } else {
            return `fa6-solid:${iconName.value}`;
        }
    } else {
        return 'ERR';
    }
});
</script>