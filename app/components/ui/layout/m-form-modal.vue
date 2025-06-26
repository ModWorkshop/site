<template>
    <m-modal v-model="vModel" :size="size" :title="title">
        <template #title>
            <h2 v-if="title">{{title}}</h2>
            <i-mdi-close class="cursor-pointer ml-auto text-xl" @click="onCancel"/>
        </template>
        <m-form @submit="onSubmit()">
            <m-flex column gap="4">
                <m-alert v-if="descType" :color="descType" :desc="desc"/>
                <span v-else-if="desc">{{desc}}</span>
                <slot/>
                <m-flex class="ml-auto" gap="1">
                    <m-button :disabled="!canSubmit" type="submit">{{saveText ?? $t('submit')}}</m-button>
                    <m-button color="danger" @click="onCancel">{{cancelText ?? $t('cancel')}}</m-button>
                </m-flex>
            </m-flex>
        </m-form>
    </m-modal>
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