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
            <div v-if="currentCanSave" class="fixed p-2" style="right: 32px; bottom: 32px; background-color: #00000040; border-radius: 3px;">
                {{$t('unsaved_changes')}}
                <a-button v-if="created" :disabled="disableButtons" color="danger" class="ml-2" @click="undo">{{$t('undo')}}</a-button>
                <a-button class="ml-2" :disabled="disableButtons" type="submit">{{currentSaveButtonText}}</a-button>
            </div>
        </Transition>
        <slot/>
    </form>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';
import { deepEqual } from 'fast-equals';
import { useI18n } from 'vue-i18n';

let props = defineProps({
    floatSaveGui: Boolean,
    canSave: Boolean,
    ignoreChanges: Object,
    created: {
        default: true,
        type: Boolean
    },
    saveText: String,
    saveButtonText: String,
    model: Object,
    models: Array
});

const emit = defineEmits(['submit', 'stateChanged']);

const disableButtons = ref(false);
const modelCopy = ref();
const { t } = useI18n();

watch(() => props.model, val => {
    disableButtons.value = false;
    modelCopy.value = clone(val);
}, { immediate: true });

const currentCanSave = computed(() => {
    return !props.created || props.canSave || !deepEqual(props.model, modelCopy.value);
});

if (props.ignoreChanges) {
    watch(props.ignoreChanges.listen, () => modelCopy.value = clone(props.model));
}

watch(currentCanSave, val => {
    emit('stateChanged', val);
});

provide('model', props.model);

const currentSaveButtonText = computed(() => props.saveButtonText || t('save'));

function submit() {
    disableButtons.value = true;
    setTimeout(() => disableButtons.value = false, 3000);
    emit('submit');
}

function undo() {
    Object.assign(props.model, modelCopy.value);
}
</script>