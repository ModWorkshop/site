<template>
    <a-modal v-model="modelValue" :size="size" @update:model-value="emit('update:modelValue', modelValue)">
        <a-form @submit="onSubmit()">
            <flex column gap="4">
                <h2 v-if="title">{{title}}</h2>
                <a-alert v-if="descType" :color="descType" :desc="desc"/>
                <span v-else-if="desc">{{desc}}</span>
                <slot/>
                <a-error-alert :error="error"/>
                <flex gap="1">
                    <a-button type="submit">{{saveText ?? $t('submit')}}</a-button>
                    <a-button color="danger" @click="onCancel()">{{cancelText ?? $t('cancel')}}</a-button>
                </flex>
            </flex>
        </a-form>
    </a-modal>
</template>

<script setup lang="ts">
const props = defineProps<{
    title?: string;
    desc?: string,
    descType?: string,
    size?: 'lg' | 'md' | 'sm',
    modelValue: boolean,
    saveText?: string
    cancelText?: string
}>();

const emit = defineEmits(['submit', 'cancel', 'update:modelValue']);

const error = ref(null);

watch(() => props.modelValue, () => error.value = null);

function onSubmit() {
    emit('submit', e => {
        error.value = e;
    });
}

function onCancel() {
    emit('cancel');
    emit('update:modelValue', false);
}
</script>