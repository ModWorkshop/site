<template>
	<m-flex column gap="1" class="w-full">
		<m-flex class="items-center">
			<label>{{ $t('files') }}</label>
			<m-button v-if="vm" class="ml-auto" @click="createNewFile">
				<i-mdi-plus-thick/>
			</m-button>
		</m-flex>

		<m-progress :percent="usedSizePercent" :text="usedSizeText" :color="fileSizeColor"/>
		<m-pagination v-model="page" :per-page="asyncFiles?.meta.per_page" :total="asyncFiles?.meta.total"/>

		<m-table alt-background>
			<template #head>
				<th style="width: 5%;"/>
				<th style="width: 15%;">{{ $t('version') }}</th>
				<th style="width: 30%;">{{ $t('name') }}</th>
				<th style="width: 10%;">{{ $t('file_size') }}</th>
				<th style="width: 30%;">{{ $t('date') }}</th>
				<th style="width: 10%;"/>
			</template>
			<template #body>
				<mod-edit-download
					v-for="file of combinedFiles"
					:key="file.id"
					:download="file"
					:mod="mod"
					:paused="paused"
					type="file"
					@remove="removeFileDialog"
					@cancel="cancelFileUpload"
					@edit="openEditFile(file as MWSFile)"
					@set-primary-download="$emit('setPrimaryDownload', 'file', file as MWSFile)"
				/>
			</template>
		</m-table>

		<m-form-modal
			v-if="currentFile"
			v-model="showEditFile"
			:title="!currentFile.id ? $t('create_file') : $t('edit_file')"
			size="lg"
			:can-submit="canSubmitFile"
			:close-on-click-outside="false"
			@submit="saveEditFile"
		>
			<m-file-uploader
				v-model="changeFile"
				:label="$t('upload_file')"
				:required="!currentFile?.id && !currentFile.actualFile"
				:storage="storageLeft + currentFile.size"
				type="file"
			/>
			<m-flex>
				<m-input v-model="currentFile.name" required :label="$t('name')"/>
				<m-input v-model="currentFile.version" :label="$t('version')"/>
			</m-flex>
			<md-editor v-model="currentFile.desc" rows="8" :label="$t('description')"/>
			<m-flex>
				<m-input v-model="currentFile.label" :label="$t('label')"/>
				<m-input v-model="currentFile.display_order" :label="$t('order')"/>
			</m-flex>
			<m-select v-model="currentFile.image_id" :label="$t('thumbnail')" :options="mod.images" :filterable="false" clearable null-clear>
				<template #any-option="{ option }">
					<m-img style="width: 150px; height: 150px; object-fit: contain" loading="lazy" url-prefix="mods/images" :src="option.file" />
				</template>
			</m-select>
		</m-form-modal>
	</m-flex>
</template>

<script setup lang="ts">
import type { Mod, File as MWSFile, PendingFileResponse } from '~/types/models';
import axios, { AxiosError, CanceledError } from 'axios';
import { useI18n } from 'vue-i18n';
import { remove } from '@antfu/utils';
import type { UploadFile, UploadSimpleFile } from '~/types/core';
import { useStore } from '~/store';

const emit = defineEmits<{
	(e: 'setPrimaryDownload', type?: 'file' | 'link', download?: MWSFile): void;
	(e: 'updateHasDownload'): void;
}>();

const { public: config } = useRuntimeConfig();

const { t } = useI18n();
const showErrorToast = useQuickErrorToast();
const yesNoModal = useYesNoModal();
const { settings, user } = useStore();

const vm = defineModel<UploadSimpleFile[]>({ default: () => [] });
const mod = defineModel<Mod>('mod', { required: true });
const uploadingFiles = ref<UploadFile[]>([]);
const currentStorage = computed(() => mod.value.used_storage ?? 0);
const allowedStorage = computed(() => mod.value.allowed_storage ? (mod.value.allowed_storage * Math.pow(1024, 2)) : null);

const combinedFiles = computed(() => ([...uploadingFiles.value, ...vm.value]));

const maxStorage = computed(() => {
	if (mod.value.user?.has_supporter_perks) {
		return Math.max(allowedStorage.value || 0, settings?.supporter_mod_storage_size || 0);
	} else {
		return allowedStorage.value || settings?.mod_storage_size || 0;
	}
});

const usedSizePercent = computed(() => 100 * (currentStorage.value / maxStorage.value));
const usedSizeText = computed(() => {
	const current = friendlySize(currentStorage.value), total = friendlySize(maxStorage.value);
	const percent = usedSizePercent.value.toFixed(1);
	return `${current}/${total} (${percent}%)`;
});
const storageLeft = computed(() => maxStorage.value - currentStorage.value);
const fileSizeColor = computed(() => usedSizePercent.value > 80 ? 'danger' : 'primary');
const paused = computed(() => mod.value.id === 0);

const showEditFile = ref(false);
const currentFile = ref<UploadFile>();
const currentFileIndex = ref<number>(-1);
const changeFile = ref<File>();
const canSubmitFile = ref(false);

const page = ref(1);

const { data: asyncFiles, refresh } = await useFetchMany<MWSFile>(`mods/${mod.value.id}/files`, {
	query: {
		limit: 20,
		include_incomplete: true,
		page: page
	},
	immediate: !!mod.value.id
});

watch(asyncFiles, () => {
	if (asyncFiles.value) {
		vm.value = asyncFiles.value.data;
	}
}, { immediate: true });

const input = ref();

watch(paused, uploadWaitingFiles);
watch(changeFile, () => {
	if (changeFile.value && currentFile.value && !currentFile.value.name) {
		currentFile.value.name ||= changeFile.value.name.split('.')[0] ?? '';
	}
});
function removeFile(file: UploadSimpleFile) {
	remove(vm.value, file);
	remove(uploadingFiles.value, file);
}

async function uploadWaitingFiles() {
	if (paused.value) {
		return;
	}

	for (const uploadFile of uploadingFiles.value) {
		if (uploadFile.waiting) {
			uploadFile.waiting = false;

			if (config.presignedUpload) {
				startThreeStageUpload(uploadFile);
			} else {
				startUpload(uploadFile);
			}
		}
	}
}

async function startUpload(uploadFile: UploadSimpleFile) {
	if (!uploadFile.actualFile) {
		return;
	}

	const formData = new FormData();
	formData.append('file', uploadFile.actualFile);

	await editOrCreateFile(uploadFile);

	try {
		const data = await patchRequest<MWSFile>(`files/${uploadFile.id}`, formData, {
			headers: { 'Content-Type': 'multipart/form-data' },
			onUploadProgress: function (progressEvent) {
				if (progressEvent.progress) {
					uploadFile.progress = progressEvent;
				}
			},
			cancelToken: new axios.CancelToken(c => uploadFile.cancel = c)
		});

		Object.assign(uploadFile, data);
		uploadFile.thumbnail = undefined;
		uploadFile.cancel = undefined;
		uploadFile.progress = undefined;

		remove(uploadingFiles.value, uploadFile);
		vm.value.unshift(uploadFile);
	} catch (e) {
		if (e instanceof AxiosError && !(e instanceof CanceledError)) {
			input.value.value = null;
			removeFile(uploadFile);
			showErrorToast(e, {}, t('failed_upload'));
		}
	}
}

async function startThreeStageUpload(uploadFile: UploadSimpleFile) {
	if (!uploadFile.actualFile) {
		return;
	}

	try {
		const actualFile = uploadFile.actualFile;

		await editOrCreateFile(uploadFile);

		const data = await postRequest<PendingFileResponse>(`files/${uploadFile.id}/begin-pending`, {
			name: actualFile.name, // TODO: check
			size: actualFile.size,
			type: actualFile.name.split('.').slice(1).join('.')
		});

		await axios.put(data.url, actualFile, {
			headers: data.headers,
			onUploadProgress: function (progressEvent) {
				if (progressEvent.progress) {
					uploadFile.progress = progressEvent;
				}
			},
			cancelToken: new axios.CancelToken(c => uploadFile.cancel = c)
		});

		const fileData = await postRequest<File>(`pending-files/${data.id}/complete`);

		if (mod.value.used_storage !== undefined) {
			mod.value.used_storage -= uploadFile.size;
			mod.value.used_storage += fileData.size;
		}

		Object.assign(uploadFile, fileData);
		uploadFile.progress = undefined;
		uploadFile.cancel = undefined;
		uploadFile.actualFile = undefined;

		remove(uploadingFiles.value, uploadFile);
		vm.value.unshift(uploadFile);

		emit('updateHasDownload');
	} catch (e) {
		if (e instanceof AxiosError && !(e instanceof CanceledError)) {
			removeFile(uploadFile);
			showErrorToast(e, {}, t('failed_upload'));
		}
	}
}

function cancelFileUpload(file: UploadSimpleFile) {
	if (file.cancel) {
		file.cancel();
	}

	const f = uploadingFiles.value.find(f => f.id === file!.id);
	if (f) {
		remove(uploadingFiles.value, f);

		f.cancel = undefined;
		f.progress = undefined;
		f.actualFile = undefined;

		vm.value.unshift({ ...f });
	}
}

/**
 * Handles removing files
 */
async function removeFileDialog(file: UploadSimpleFile) {
	yesNoModal({
		title: t('are_you_sure'),
		desc: t('delete_file_desc'),
		yes: async () => await handleRemove(file)
	});
}

async function handleRemove(file: UploadSimpleFile) {
	if (file.cancel) {
		file.cancel('cancelled');
	} else if (file.id) {
		await deleteRequest(`files/${file.id}`);
	}

	removeFile(file);

	if (mod.value.download_id === file.id) {
		emit('setPrimaryDownload');
	}

	emit('updateHasDownload');

	if (mod.value.used_storage && file.id) {
		mod.value.used_storage -= file.size;
	}

	if (vm.value.length === 0) {
		page.value = 1;
		refresh();
	}
}

function openEditFile(file: UploadFile) {
	showEditFile.value = true;
	currentFileIndex.value = uploadingFiles.value.findIndex(f => f === file);
	currentFile.value = { ...file };

	canSubmitFile.value = true;

	changeFile.value = undefined;
}

async function editOrCreateFile(file: UploadSimpleFile) {
	let newFile: UploadSimpleFile;
	if (file.id) {
		newFile = await patchRequest(`files/${file.id}`, file);
	} else {
		file.name ??= file.actualFile?.name ?? 'Unknown File';
		newFile = await postRequest<MWSFile>(`mods/${mod.value.id}/files`, file);
	}

	for (const f of combinedFiles.value) {
		if (f === file || f.id === file?.id) {
			Object.assign(f, newFile);
		}
	}
}

async function saveEditFile() {
	canSubmitFile.value = false;
	const file: UploadFile | undefined = currentFile.value;
	if (!file) {
		return;
	}

	if (!changeFile.value) { // File wasn't changed, but let's allow changing some values
		if (paused.value) { // Mod wasn't uploaded yet
			Object.assign(uploadingFiles.value[currentFileIndex.value]!, file);
		} else {
			await editOrCreateFile(file);
		}
	} else {
		file.actualFile = changeFile.value;
		file.waiting = true;

		if (file.cancel) {
			file.cancel();
		}

		// Since the file is now uploading, we need to remove it from the files for 0 interfering
		const f = vm.value.find(f => f.id === file!.id);
		if (f) {
			remove(vm.value, f);
		}
		if (currentFileIndex.value !== -1) { // Change in-progress file
			Object.assign(uploadingFiles.value[currentFileIndex.value]!, f);
		} else {
			uploadingFiles.value.unshift(file);
		}

		uploadWaitingFiles();
	}

	showEditFile.value = false;
}

function createNewFile() {
	openEditFile({
		id: 0,
		user_id: user!.id,
		mod_id: mod.value.id,
		size: 0,
		name: '',
		desc: '',
		label: '',
		version: '',
		downloads: 0,
		file: '',
		type: '',
		display_order: 0,
		download_url: '',
		image_id: null,
		approved: true
	});
}
</script>
