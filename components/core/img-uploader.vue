<template>
    <a-input>
        <label class="flexbox items-end gap-2" :for="id">
            <slot name="label" :src="currentSrc">
                <a-img class="w-full round" :src="currentSrc"/>
            </slot>
        </label>
        <input class="mt-1" ref="input" type="file" :id="id" @change="onChange"/>
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
const input = ref();
const currentSrc = computed(() => blob.value || props.src && `http://localhost:8000/storage/${props.src}` || '');

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