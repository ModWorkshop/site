<template>
    <va-modal v-model="modelValue" size="large" background-color="#2b3036" no-outside-dismiss @update:model-value="emit('update:modelValue', modelValue)">
        <template #content="{ ok }">
            <flex column gap="4">
                <h2 v-if="title">{{title}}</h2>
                <va-alert v-if="desc" class="whitespace-pre" color="danger" :description="desc"/>
                <slot/>
                <a-error-alert :error="error"/>
                <flex gap="1">
                    <a-button @click="onSave(ok)">{{$t('save')}}</a-button>
                    <a-button color="danger" @click="onCancel(ok)">{{$t('cancel')}}</a-button>
                </flex>
            </flex>
        </template>
    </va-modal>
</template>

<script setup lang="ts">
defineProps({
    title: String,
    desc: String,
    modelValue: Boolean
});

const emit = defineEmits(['save', 'cancel', 'update:modelValue']);

const error = ref(null);

function onSave(ok: () => void) {
    emit('save', ok, e => {
        error.value = e;
    });
}

function onCancel(ok: () => void) {
    emit('cancel');
    ok();
}
</script>