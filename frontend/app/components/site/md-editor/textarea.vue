<template>
    <textarea
        :id="labelId"
        ref="element"
        v-model="vm"
        type="textarea" 
        :class="{textarea: true, 'input-error': !!err}"
        :rows="rows" 
        v-bind="$attrs" 
    />
</template>

<script setup lang="ts">
const props = defineProps({
    labelId: String,
    modelValue: String,
    rows: { type: [String, Number], default: 12 }
});

const emit = defineEmits(['update:modelValue', 'textarea-keyup']);
const vm = useVModel(props, 'modelValue', emit);

const element = ref();
const err = inject<Ref<boolean>>('err');

defineExpose({
    element
});
</script>

<style scoped>

</style>