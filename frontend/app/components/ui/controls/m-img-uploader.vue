<template>
	<m-input :id="labelId">
		<template #label>
			<slot name="label"/>
		</template>
		<label class="flex items-end gap-2" :for="labelId">
			<slot name="image" :src="currentSrc">
				<m-img class="round" loading="lazy" :src="currentSrc" :url-prefix="urlPrefix" :width="width" :height="height"/>
			</slot>
		</label>
		<m-flex>
			<m-button v-if="(localClearButton && blob) || (clearButton && currentSrc)" @click="clearImage"><i-mdi-remove/></m-button>
			<m-input :id="labelId" v-model:element-ref="input" :disabled="disabled" type="file" @update:model-value="onChange"/>
		</m-flex>
	</m-input>
</template>

<script setup lang="ts">
const { src, maxFileSize, id, localClearButton = true } = defineProps<{
	id?: string;
	src?: string;
	urlPrefix?: string;
	width?: string | number;
	height?: string | number;
	disabled?: boolean;
	clearButton?: boolean;
	localClearButton?: boolean;
	maxFileSize?: number | string;
}>();

const modelValue = defineModel<Blob | '' | null | undefined>();
const { showToast } = useToaster();
const { t } = useI18n();

const fileRef = ref();
const input = ref<HTMLInputElement>();
const blob = ref();
const currentSrc = computed(() => {
	if (modelValue.value !== '') {
		return blob.value || src;
	}
});

const uniqueId = useId();
const labelId = computed(() => id || uniqueId);

const maxFileSizeBytes = computed(() => parseInt(maxFileSize as string));

watch(modelValue, (value, oldValue) => {
	if (input.value && oldValue && !value) {
		blob.value = null;
		input.value.value = '';
	}
});

function clearImage() {
	if (src && !blob.value) {
		modelValue.value = '';
	} else {
		modelValue.value = undefined;
	}
}

function onChange() {
	const file = input.value?.files?.[0];
	if (file) {
		if (maxFileSizeBytes.value && file.size > maxFileSizeBytes.value) {
			showToast({
				desc: t('file_name_too_large', { name: file.name }),
				color: 'danger'
			});
			return;
		}

		const reader = new FileReader();
		reader.addEventListener('load', () => blob.value = reader.result);

		if (file instanceof Blob) {
			reader.readAsDataURL(file);
		}
	}

	fileRef.value = file;
	modelValue.value = file;
}
</script>
