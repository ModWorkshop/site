<template>
    <!-- 
        The point of this form is as follows: nice save UI (optional) & validation rules
        The save UI uses deep checking to make sure things are equal and triggers a save when things are not.
        Note: items that shouldn't be tracked should be plucked from the model
        For example, if we get files & images for a mod, we are not interested
        in saving them with the mod (as these are handled separately)
        So we should pluck them from the mod we get from the API and save them elsewhere.
    -->
    <form @submit.prevent="submit">
        <!-- Dunno why SSR really dislikes me doing the deep check, anyway this shouldn't be relevant for SSR. Though check in Nuxt3 if it's fixed. -->
        <client-only v-if="floatSaveGui">
            <transition name="fade">
                <div v-if="currentCanSave" class="fixed p-2" style="right: 32px; bottom: 32px; background-color: #00000040; border-radius: 3px;">
                    <small>{{currentSaveText}}</small>
                    <a-button type="submit">{{currentSaveButtonText}}</a-button>
                </div>
            </transition>
        </client-only>
        <slot/>
    </form>
</template>

<script setup>
    import { computed, provide, watch } from '@nuxtjs/composition-api';
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
        model: Object
    });
    
    let modelCopy = $ref();
    watch(() => props.model, val => {
        modelCopy = clone(val);
    }, {immediate: true});

    let currentCanSave = computed(() => {
        return props.canSave || !deepEqual(props.model, modelCopy);
    });

    defineEmits(['submit']);
    provide('rules', props.rules);
    provide('model', props.model);

    const currentSaveButtonText = computed(function() {
        return props.saveButtonText || (props.created ? 'save' : 'upload');
    });

    const currentSaveText = computed(function() {
        return this.$t('unsaved_changes');
    });

    function submit() {
        this.$emit('submit');
    }
</script>

<style>
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.25s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
</style>