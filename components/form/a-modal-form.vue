<template>
    <va-modal v-model="modelValue" :size="size" background-color="#2b3036" @update:model-value="emit('update:modelValue', modelValue)">
        <template #content="{ ok }">
            <a-form @submit="onSubmit(ok)">
            <flex column gap="4">
                    <h2 v-if="title">{{title}}</h2>
                    <a-alert v-if="descType" :color="descType" :desc="desc"/>
                    <span v-else-if="desc">{{desc}}</span>
                    <slot/>
                    <a-error-alert :error="error"/>
                    <flex gap="1">
                        <a-button type="submit">{{saveText ?? $t('submit')}}</a-button>
                        <a-button color="danger" @click="onCancel(ok)">{{cancelText ?? $t('cancel')}}</a-button>
                    </flex>
                </flex>
            </a-form>
        </template>
    </va-modal>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    title?: string;
    desc?: string,
    descType?: string,
    size?: 'large' | 'medium' | 'small',
    modelValue: boolean,
    saveText?: string
    cancelText?: string
}>(), { size: 'large' });

const emit = defineEmits(['submit', 'cancel', 'update:modelValue']);

const error = ref(null);

watch(() => props.modelValue, () => error.value = null);

function onSubmit(ok: () => void) {
    emit('submit', ok, e => {
        error.value = e;
    });
}

function onCancel(ok: () => void) {
    emit('cancel');
    ok();
}
</script>