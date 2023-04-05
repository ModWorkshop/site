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

const emit = defineEmits(['submit', 'discard', 'stateChanged']);

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

function discard() {
    if (props.model) {
        Object.assign(props.model, modelCopy.value);
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