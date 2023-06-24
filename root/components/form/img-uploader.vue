<template>
    <a-input :id="labelId">
        <label class="flex items-end gap-2" :for="labelId">
            <slot name="label" :src="currentSrc">
                <a-img class="w-full round" loading="lazy" :src="currentSrc" :url-prefix="urlPrefix"/>
            </slot>
        </label>
        <input :id="labelId" ref="input" class="mt-1" type="file" @change="onChange">
    </a-input>
</template>

<script setup lang="ts">
const props = defineProps({
    modelValue: Blob,
    id: String,
    src: String,
    urlPrefix: String,
});

const emit = defineEmits(['update:modelValue']);

const fileRef = ref();
const input = ref<HTMLInputElement>();
const blob = ref();
const currentSrc = computed(() => blob.value || props.src);

const uniqueId = useGetUniqueId();
const labelId = computed(() => props.id || uniqueId);

watch(() => props.modelValue, (value, oldValue) => {
    if (input.value && oldValue && !value) {
        blob.value = null;
        input.value.value = '';
    }
});

function onChange() {
    const file = input.value?.files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            blob.value = reader.result; 
        };

        if (file instanceof Blob) {
            reader.readAsDataURL(file);    
        }
    }

    fileRef.value = file;
    emit('update:modelValue', file);
}
</script>