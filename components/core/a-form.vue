<template>
    <form @submit.prevent="submit">
        <!-- 
            The point of this form is as follows: nice save UI (optional) & validation rules
            The save UI uses deep checking to make sure things are equal and triggers a save when things are not.
            Note: items that shouldn't be tracked should be plucked from the model
            For example, if we get files & images for a mod, we are not interested
            in saving them with the mod (as these are handled separately)
            So we should pluck them from the mod we get from the API and save them elsewhere.
        -->
        <transition name="fade" v-if="floatSaveGui">
            <div v-if="currentCanSave" class="fixed p-2" style="right: 32px; bottom: 32px; background-color: #00000040; border-radius: 3px;">
                {{$t('unsaved_changes')}}
                <a-button class="ml-2" type="submit">{{currentSaveButtonText}}</a-button>
            </div>
        </transition>
        <slot/>
    </form>
</template>

<script setup>
    import clone from 'rfdc/default';
    import { deepEqual } from 'fast-equals';

    let props = defineProps({
        floatSaveGui: Boolean,
        canSave: Boolean,
        created: {
            default: true,
            type: Boolean
        },
        saveText: String,
        saveButtonText: String,
        rules: Object,
        model: Object,
        models: Array
    });
    
    const modelCopy = ref();
    watch(() => props.model, val => {
        modelCopy.value = clone(val);
    }, { immediate: true });

    const currentCanSave = computed(() => {
        return !props.created || props.canSave || !deepEqual(props.model, modelCopy.value);
    });

    const emit = defineEmits(['submit']);
    provide('rules', props.rules);
    provide('model', props.model);

    const currentSaveButtonText = computed(function() {
        return props.saveButtonText || (props.created ? 'save' : 'upload');
    });

    function submit() {
        emit('submit');
    }
</script>