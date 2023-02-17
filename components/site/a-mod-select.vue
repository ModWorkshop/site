<template>
    <a-select v-model="vm" :url="url">
        <template #any-option="{ option }">
            <a-list-item :item="option">
                <template #before-item>
                    <mod-thumbnail :thumbnail="option.thumbnail" style="width: 100px;"/>
                </template>
            </a-list-item>
        </template>
    </a-select>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    modelValue?: number;
    url: string
}>(), { url: 'mods' });


const emit = defineEmits(['update:modelValue']);
const vm = useVModel(props, 'modelValue', emit);
</script>