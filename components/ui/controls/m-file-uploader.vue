<template>
    <m-input :id="labelId">
        <template #label>
            <slot name="label"/>
        </template>
        <m-flex>
            <m-button v-if="(localClearButton && fileRef) || (modelValue && clearButton)" @click="clear"><i-mdi-remove/></m-button>
            <m-input :id="labelId" v-model:element-ref="input" :disabled="disabled" type="file" @change="onChange"/>
        </m-flex>
        <m-progress v-if="progress" :percent="progress * 100" :show-text="false" :height="4"/>
    </m-input>
</template>

<script setup lang="ts">
const { maxFileSize, id, localClearButton = true } = defineProps<{
    id?: string,
    urlPrefix?: string,
    disabled?: boolean,
    clearButton?: boolean,
    localClearButton?: boolean,
    progress?: number,
    maxFileSize?: number|string,
}>();

const modelValue = defineModel<File|undefined>();
const { showToast } = useToaster();
const { t } = useI18n();

const fileRef = ref();
const input = ref<HTMLInputElement>();

const uniqueId = useId();
const labelId = computed(() => id || uniqueId);

const maxFileSizeBytes = computed(() => parseInt(maxFileSize as string));

watch(modelValue, (value, oldValue) => {
    if (input.value && oldValue && !value) {
        input.value.value = '';
    }
});

function clear() {
    modelValue.value = undefined;
}

function onChange() {
    const file = input.value?.files?.[0];
    if (file) {
        if (maxFileSizeBytes.value && file.size > maxFileSizeBytes.value) {
            showToast({ 
                desc: t('file_name_too_large', { name: file.name }),
                color: 'danger'
            });
            return;
        }
    }

    fileRef.value = file;
    modelValue.value = file;
}
</script>