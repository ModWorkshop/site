<template>
    <a-input :id="labelId">
        <label class="flexbox items-end gap-2" :for="labelId">
            <slot name="label" :src="currentSrc">
                <a-img class="w-full round" :src="currentSrc"/>
            </slot>
        </label>
        <input :id="labelId" ref="input" class="mt-1" type="file" @change="onChange">
    </a-input>
</template>

<script setup lang="ts">
const props = defineProps({
    modelValue: Blob,
    id: String,
    src: String
});

const emit = defineEmits(['update:modelValue']);

const fileRef = ref();
const blob = ref();
const input = ref<HTMLInputElement>();
const currentSrc = computed(() => blob.value || props.src && `http://localhost:8000/storage/${props.src}` || '');

const uniqueId = useGetUniqueId();
const labelId = computed(() => props.id || uniqueId);

watch(() => props.modelValue, (value, oldValue) => {
    if (input.value && oldValue && !value) {
        input.value.value = null;
    }
});

function onChange() {
    const file = input.value.files[0];
    const reader = new FileReader();
    reader.onload = () => {
        blob.value = reader.result;
    };
    fileRef.value = file;
    emit('update:modelValue', file);
    console.log(file);
    
    if (file instanceof Blob) {
        reader.readAsDataURL(file);    
    }
}
</script>