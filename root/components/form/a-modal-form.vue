<template>
    <a-modal v-model="vModel" :size="size">
        <a-form @submit="onSubmit()">
            <flex column gap="4">
                <h2 v-if="title">{{title}}</h2>
                <a-alert v-if="descType" :color="descType" :desc="desc"/>
                <span v-else-if="desc">{{desc}}</span>
                <slot/>
                <flex gap="1">
                    <a-button :disabled="!canSubmit" type="submit">{{saveText ?? $t('submit')}}</a-button>
                    <a-button color="danger" @click="onCancel()">{{cancelText ?? $t('cancel')}}</a-button>
                </flex>
            </flex>
        </a-form>
    </a-modal>
</template>

<script setup lang="ts">
const { canSubmit = true } = defineProps<{
    title?: string;
    desc?: string;
    descType?: string;
    size?: 'lg' | 'md' | 'sm';
    saveText?: string;
    cancelText?: string;
    canSubmit?: boolean;
}>();

const emit = defineEmits(['submit', 'cancel']);
const vModel = defineModel<boolean>({ required: true });
const showToast = useQuickErrorToast();

function onSubmit() {
    emit('submit', e => {
        showToast(e);
    });
}

function onCancel() {
    emit('cancel', e => {
        showToast(e);
    });
    vModel.value = false;
}
</script>