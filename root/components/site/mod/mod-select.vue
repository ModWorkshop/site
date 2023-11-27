<template>
    <m-select v-model="vm" :url="url">
        <template #any-option="{ option }">
            <m-list-item :item="option">
                <template #before-item>
                    <mod-thumbnail :thumbnail="option.thumbnail" style="width: 100px;"/>
                </template>
            </m-list-item>
        </template>
    </m-select>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    modelValue?: number;
    url: string
}>(), { url: 'mods' });


const emit = defineEmits(['update:modelValue']);
const vm = useVModel(props, 'modelValue', emit);
</script>