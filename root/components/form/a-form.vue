<!-- 
    The point of this form is as follows: nice save UI (optional)
    The save UI uses deep checking to make sure things are equal and triggers a save when things are not.
    Note: items that shouldn't be tracked should be plucked from the model
    For example, if we get files & images for a mod, we are not interested
    in saving them with the mod (as these are handled separately)
    So we should pluck them from the mod we get from the API and save them elsewhere.
-->

<template>
    <form @submit.prevent="submit">
        <Transition v-if="floatSaveGui">
            <flex v-if="currentCanSave" class="fixed float-bg round float-save items-center">
                {{$t('unsaved_changes')}}
                <a-button v-if="created" :disabled="disableButtons" color="danger" class="ml-2" @click="discard">{{$t('discard')}}</a-button>
                <a-button :disabled="disableButtons" type="submit">{{currentSaveButtonText}}</a-button>
            </flex>
        </Transition>
        <slot/>
    </form>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';
import { deepEqual } from 'fast-equals';
import { useI18n } from 'vue-i18n';

const { created = true, saveButtonText, flushChanges, canSave, transformForCompare } = defineProps<{
    transformForCompare?: (a, b) => void,
    floatSaveGui?: boolean,
    canSave?: boolean,
    flushChanges?: EventRaiser,
    created?: boolean,
    saveText?: string,
    saveButtonText?: string
}>();

const model = defineModel<object>();

const emit = defineEmits(['submit', 'discard', 'stateChanged']);

const disableButtons = ref(false);
const modelCopy = ref<object>();
const { t } = useI18n();

const currentCanSave = computed(() => {
    let A = model.value, B = modelCopy.value;
    if (transformForCompare) {
        A = clone(A);
        B = clone(B);

        transformForCompare(A, B);
    }

    return !created || canSave || !deepEqual(A, B);
});


watch(model, () => {
    disableButtons.value = false;
    modelCopy.value = clone(model.value);
}, { immediate: true });

watch(flushChanges?.listen ?? ref(), () => {
    disableButtons.value = false;
    modelCopy.value = clone(model.value);
});

watch(currentCanSave, val => {
    emit('stateChanged', val);
});

const currentSaveButtonText = computed(() => saveButtonText || (created ? t('save') : t('submit')));

function submit() {
    disableButtons.value = true;
    setTimeout(() => disableButtons.value = false, 3000);
    emit('submit');
}

function discard() {
    if (model.value) {
        Object.assign(model.value, modelCopy.value);
    }
    emit('discard');
}
</script>

<style scoped>
.float-save {
    transform: translateX(-50%);
    left: 50%;
    bottom: 16px;
    padding: 1rem;
    z-index: 100;
}
</style>