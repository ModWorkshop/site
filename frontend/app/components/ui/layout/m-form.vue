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
			<m-flex v-if="currentCanSave" class="fixed float-bg content-block round float-save items-center">
				<span class="mr-2">
					{{ $t('unsaved_changes') }}
				</span>
				<m-button v-if="created" :disabled="disableButtons" color="danger" @click="discard">{{ $t('discard') }}</m-button>
				<m-button :disabled="disableButtons" type="submit">{{ currentSaveButtonText }}</m-button>
			</m-flex>
		</Transition>
		<slot/>
	</form>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';
import { deepEqual } from 'fast-equals';
import { useI18n } from 'vue-i18n';
import type { EventHook } from '@vueuse/core';

const { created = true, saveButtonText, flushChanges, canSave, preCompare, excludeFromCompare } = defineProps<{
	preCompare?: (v) => void;
	excludeFromCompare?: string[];
	floatSaveGui?: boolean;
	canSave?: boolean;
	flushChanges?: EventHook<object>;
	created?: boolean;
	saveText?: string;
	saveButtonText?: string;
}>();

const model = defineModel<object>();

const emit = defineEmits(['submit', 'discard', 'stateChanged']);

const disableButtons = ref(false);
const modelCopy = ref<object>();
const { t } = useI18n();

const currentCanSave = computed(() => {
	let A = model.value, B = modelCopy.value;
	if (preCompare || excludeFromCompare) {
		A = clone(A);
		B = clone(B);
	}

	if (preCompare) {
		preCompare(A);
		preCompare(B);
	}
	if (excludeFromCompare) {
		if (A) {
			excludeFromCompare.forEach(key => delete A![key]);
		}
		if (B) {
			excludeFromCompare.forEach(key => delete B![key]);
		}
	}

	return !created || canSave || !deepEqual(A, B);
});

disableButtons.value = false;
modelCopy.value = clone(model.value);

watch(() => flushChanges, () => {
	flushChanges?.on(newModel => {
		disableButtons.value = false;
		modelCopy.value = clone(newModel);
		model.value = clone(modelCopy.value);
	});
}, { immediate: true });

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
		Object.assign(model.value, clone(modelCopy.value));
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
