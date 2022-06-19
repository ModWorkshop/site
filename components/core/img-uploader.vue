<template>
    <div>
        <label class="flex items-end gap-2" :for="id">
            <slot name="label" :src="currentSrc">
                <a-img class="w-full round" :src="currentSrc"/>
            </slot>
        </label>
        <input ref="input" type="file" :id="id" @change="onChange"/>
    </div>
</template>
<script setup>
const props = defineProps([
    'id',
    'src',
    'file'
]);

const fileRef = ref();
const blob = ref();
const input = ref();
const currentSrc = computed(() => blob.value || props.src && `http://localhost:8000/storage/${props.src}` || '');

function onChange() {
    const file = input.value.files[0];
    const reader = new FileReader(file);
    reader.onload = () => {
        blob.value = reader.result;
    };
    fileRef.value = file;
    this.$emit('update:file', file);
    if (file instanceof Blob) {
        reader.readAsDataURL(file);    
    }
}
</script>