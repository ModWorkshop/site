<template>
	<m-input :id="labelId" :required="required">
		<template #label>
			<slot name="label"/>
		</template>
		<m-flex>
			<input :id="labelId" ref="input" :disabled="disabled" type="file" class="kinda-hidden" @change="onChange">
			<input :disabled="disabled" class="kinda-hidden mr-auto" :value="modelValue ? 'a' : ''" :required="required">
			<label :class="{'mw-input': true, 'm-file-uploader-path': true, 'cursor-pointer': !disabled}" :for="labelId">
				<m-flex class="mx-auto items-center text-center text-secondary" column gap="2">
					<i-mdi-upload class="upload-icon"/>
					<span v-if="!modelValue">
						{{ $t('file_uploader_drop_single') }}
					</span>
					<span v-else class="text-body">
						{{ modelValue?.name }}
					</span>
				</m-flex>

				<i-mdi-remove
					v-if="!disabled && ((localClearButton && fileRef) || (modelValue && clearButton))"
					class="absolute"
					style="right: 16px;"
					@click.prevent="clear"
				/>
			</label>
		</m-flex>
		<m-uploader-progress v-if="progress?.progress" :progress="progress"/>
	</m-input>
</template>

<script setup lang="ts">
import type { AxiosProgressEvent, Canceler } from 'axios';

const { maxFileSize, storage, id, localClearButton = true, cancel } = defineProps<{
	id?: string;
	urlPrefix?: string;
	disabled?: boolean;
	required?: boolean;
	clearButton?: boolean;
	localClearButton?: boolean;
	maxFileSize?: number | string;
	storage?: number | string;
	cancel?: Canceler;
}>();

const modelValue = defineModel<File | undefined>();
const progress = defineModel<AxiosProgressEvent>('progress');
const { showToast } = useToaster();
const { t } = useI18n();

const fileRef = ref();
const input = ref<HTMLInputElement>();

const uniqueId = useId();
const labelId = computed(() => id || uniqueId);

const maxFileSizeBytes = computed(() => parseInt(maxFileSize as string));
const storageBytes = computed(() => parseInt(storage as string));

watch(modelValue, (value, oldValue) => {
	if (input.value && oldValue && !value) {
		input.value.value = '';
		progress.value = undefined;
	}
}, { immediate: true });

function clear() {
	if (cancel) {
		cancel();
	}
	modelValue.value = undefined;
	fileRef.value = undefined;
}

function onChange() {
	const file = input.value?.files?.[0];
	if (file) {
		if (maxFileSizeBytes.value && file.size > maxFileSizeBytes.value) {
			showToast({
				desc: t('file_name_too_large', { name: file.name }),
				color: 'danger'
			});
			if (input.value) {
				input.value.value = '';
			}
			return;
		}

		if (file.size > storageBytes.value) {
			showToast({
				desc: t('file_name_too_large_max_size', { name: file.name }),
				color: 'danger'
			});
			if (input.value) {
				input.value.value = '';
			}
			return;
		}
	}

	fileRef.value = file;
	modelValue.value = file;
}
</script>

<style>
.m-file-uploader-path {
	padding: 1.5rem 1rem;
	display: flex;
	align-items: center;
	position: relative;
}

.upload-icon {
	font-size: 1rem;
	color: var(--secondary-text-color);
	background-color: rgba(255, 255, 252, 0.1);
	border-radius: 100%;
	padding: 0.5rem;
}
</style>
