<template>
	<m-flex class="w-full mb-4">
		<span class="h2">{{ $t('downloads') }}</span>
		<m-button class="ml-auto" @click="setPrimaryDownload()"><i-mdi-close/> {{ $t('clear_primary_download') }}</m-button>
	</m-flex>

	<label>{{ $t('files') }}</label>
	<m-flex column>
		<m-progress :percent="usedSizePercent" :text="usedSizeText" :color="fileSizeColor"/>
		<m-uploader
			v-model="files"
			list
			name="files"
			:upload-url="uploadLink"
			:paused="!mod.id"
			:paused-reason="$t('file_waiting')"
			:max-size="maxStorage"
			url="files"
			ask-before-remove
			:presigned-upload="config.presignedUpload"
			@file-uploaded="fileUploaded"
			@file-deleted="fileDeleted"
		>
			<template #before-info="{ file }">
				<input
					:checked="(file.id === mod.download_id && mod.download_type == 'file') ? true : undefined"
					type="radio"
					:disabled="file.waiting || !!file.progress"
					@change="setPrimaryDownload('file', file as MWSFile)"
				>
			</template>
			<template #after-buttons="{ file }">
				<m-button class="file-button" :disabled="file.waiting || !!file.progress" @click.prevent="editFile(file as MWSFile)">
					<i-mdi-cog/>
				</m-button>
			</template>
		</m-uploader>
		<m-pagination v-model="filesPage" :per-page="10" :total="asyncFiles?.meta.total"/>
	</m-flex>

	<m-flex column>
		<m-flex class="items-center">
			<label>{{ $t('links') }}</label>
			<m-button v-if="links" class="ml-auto" @click="createNewLink">
				<i-mdi-plus-thick/>
			</m-button>
		</m-flex>
		<m-flex column>
			<template v-if="links.length">
				<m-content-block v-for="link of links" :key="link.id" alt-background :column="false" wrap class="items-center">
					<input :checked="(link.id === mod.download_id && mod.download_type == 'link') ? true : undefined" type="radio" @change="setPrimaryDownload('link', link)">
					<m-flex column class="break-words overflow-hidden">
						<span>
							{{ link.name }} ({{ link.url }})
						</span>
						<m-time v-if="link.id && link.updated_at" :datetime="link.updated_at"/>
						<span v-else>{{ $t('waiting_for_mod') }}</span>
					</m-flex>
					<m-flex class="ml-auto">
						<span class="text-center">
							<m-flex inline>
								<m-button color="danger" @click.prevent="deleteLink(link)"><i-mdi-delete/></m-button>
								<m-button @click.prevent="editLink(link)"><i-mdi-cog/></m-button>
							</m-flex>
						</span>
					</m-flex>
				</m-content-block>
			</template>
			<span v-else class="text-center">
				{{ $t('nothing_found') }}
			</span>
		</m-flex>
		<m-pagination v-model="linksPage" :per-page="10" :total="asyncLinks?.meta.total"/>
	</m-flex>

	<span class="h2 my-4">{{ $t('updates') }}</span>

	<m-input v-if="!light" v-model="mod.version" :label="$t('version')"/>
	<m-input v-if="!light" v-model="mod.repo_url" :label="$t('repo_url')" type="url"/>
	<md-editor v-if="!light" v-model="mod.changelog" :label="$t('changelog')" rows="12"/>
	<m-input v-if="!light" v-model="mod.disable_mod_managers" :label="$t('disable_mod_managers')" :desc="$t('disable_mod_managers_desc')" type="checkbox"/>
	<m-input v-if="canModerate && !light" v-model="mod.allowed_storage" type="number" max="1000" :label="$t('allowed_storage')" :desc="$t('allowed_storage_help')"/>

	<m-form-modal
		v-if="currentLink"
		v-model="showEditLink"
		:title="$t('edit_link')"
		:close-on-click-outside="false"
		size="lg" @submit="saveEditLink"
	>
		<m-flex>
			<m-input v-model="currentLink.name" required :label="$t('name')"/>
			<m-input v-model="currentLink.label" :label="$t('label')"/>
		</m-flex>
		<m-input v-model="currentLink.url" type="url" required :label="$t('url')"/>
		<m-input v-model="currentLink.version" :label="$t('version')"/>
		<m-input v-model="currentLink.display_order" :label="$t('order')"/>
		<m-select v-model="currentLink.image_id" :label="$t('thumbnail')" :options="mod.images" :filterable="false" clearable>
			<template #any-option="{ option }">
				<m-img style="width: 100px; height: 100px; object-fit: contain" loading="lazy" url-prefix="mods/images" :src="option.file" />
			</template>
		</m-select>
		<md-editor v-model="currentLink.desc" rows="8" :label="$t('description')"/>
	</m-form-modal>

	<m-form-modal
		v-if="currentFile"
		v-model="showEditFile"
		:title="$t('edit_file')"
		size="lg"
		:can-submit="canSubmitFile"
		:close-on-click-outside="false"
		@submit="saveEditFile"
		@cancel="cancelEditFile"
	>
		<m-flex>
			<m-input v-model="currentFile.name" :label="$t('name')"/>
			<m-input v-model="currentFile.label" :label="$t('label')"/>
		</m-flex>
		<m-input v-model="currentFile.version" :label="$t('version')"/>
		<m-input v-model="currentFile.display_order" :label="$t('order')"/>
		<m-file-uploader
			v-model="changeFile"
			v-model:progress="changeFileProgress"
			:cancel="cancelFileUpload"
			:label="$t('upload_file')"
			:disabled="disableChangeFile"
			type="file"
		/>
		<m-select v-model="currentFile.image_id" :label="$t('thumbnail')" :options="mod.images" :filterable="false" clearable>
			<template #any-option="{ option }">
				<m-img style="width: 150px; height: 150px; object-fit: contain" loading="lazy" url-prefix="mods/images" :src="option.file" />
			</template>
		</m-select>
		<md-editor v-model="currentFile.desc" rows="8" :label="$t('description')"/>
	</m-form-modal>
</template>

<script setup lang="ts">
import type { File as MWSFile, Link, Mod, PendingFileResponse } from '~/types/models';
import clone from 'rfdc/default';
import { useStore } from '~/store';
import axios, { CanceledError, type AxiosProgressEvent, type Canceler } from 'axios';
import { remove } from '@antfu/utils';

const { settings, hasPermission, user } = useStore();
const { public: config } = useRuntimeConfig();
const showError = useQuickErrorToast();

defineProps<{
	light?: boolean;
}>();

const mod = defineModel<Mod>({ required: true });
const canModerate = computed(() => hasPermission('manage-mods', mod.value.game));
const currentStorage = computed(() => mod.value.used_storage ?? 0);
const allowedStorage = computed(() => mod.value.allowed_storage ? (mod.value.allowed_storage * Math.pow(1024, 2)) : null);

// Edit file modal stuff
const showEditFile = ref(false);
const currentFile = ref<MWSFile>();
const changeFile = ref<File>();
const changeFileProgress = ref<AxiosProgressEvent>();
const canSubmitFile = ref(false);
const cancelFileUpload = ref<Canceler>();
const disableChangeFile = ref(false);

// Edit link modal stuff
const showEditLink = ref(false);
const currentLink = ref<Link>();

const filesPage = ref(1);
const linksPage = ref(1);

const { data: asyncFiles, refresh: refreshFiles } = await useWatchedFetchMany(`mods/${mod.value.id}/files`, {
	limit: 10,
	page: filesPage
}, { immediate: !!mod.value.id });
const { data: asyncLinks, refresh: refreshLinks } = await useWatchedFetchMany(`mods/${mod.value.id}/links`, {
	limit: 10,
	page: linksPage
}, { immediate: !!mod.value.id });

const filesRef = ref<MWSFile[]>([]);
const linksRef = ref<Link[]>([]);

const files = computed<MWSFile[]>(() => asyncFiles.value?.data ?? filesRef.value);
const links = computed<Link[]>(() => asyncLinks.value?.data ?? linksRef.value);

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
const fileSizeColor = computed(() => usedSizePercent.value > 80 ? 'danger' : 'primary');
const uploadLink = computed(() => `mods/${mod.value.id}/files`);

// Handle mod submit
watch(() => mod.value.id, async () => {
	for (const link of links.value) {
		if (!link.id) {
			const newLink = await postRequest<Link>(`mods/${mod.value.id}/links`, link);
			Object.assign(link, newLink);
		}
	}
});

watch(changeFile, async file => {
	if (currentFile.value && file && config.presignedUpload) {
		try {
			const data = await postRequest<PendingFileResponse>(`files/${currentFile.value.id}/begin-pending`, {
				name: file.name,
				size: file.size,
				type: file.name.split('.').slice(1).join('.')
			});

			await axios.put(data.url, file, {
				headers: data.headers,
				onUploadProgress: function (progressEvent) {
					if (progressEvent.progress) {
						changeFileProgress.value = progressEvent;
					}
				},
				cancelToken: new axios.CancelToken(c => cancelFileUpload.value = c)
			});

			disableChangeFile.value = true;

			const fileData = await postRequest<MWSFile>(`pending-files/${data.id}/complete`);
			changeFileProgress.value = undefined;
			changeFile.value = undefined;

			disableChangeFile.value = false;

			if (mod.value.used_storage) {
				mod.value.used_storage -= currentFile.value.size + fileData.size;
			}

			Object.assign(currentFile.value, fileData);
			for (const f of files.value) {
				if (f.id === currentFile.value.id) {
					Object.assign(f, file);
				}
			}
		} catch (e) {
			if (!(e instanceof CanceledError)) {
				showError(e);
			}
		}
	}
});

function updateHasDownload() {
	mod.value.files_count = files.value.length ?? 0;
	mod.value.links_count = links.value.length ?? 0;
	mod.value.has_download = (mod.value.files_count > 0) || (mod.value.links_count > 0) || false;

	if (Math.abs(mod.value.files_count - mod.value.links_count) === 1) {
		mod.value.download = files.value[0] ?? links.value[0];
	}
}

function setPrimaryDownload(type?: 'file' | 'link', download?: MWSFile | Link) {
	mod.value.download_type = type ?? null;
	mod.value.download_id = (download && download.id) ?? null;
	mod.value.download = download;
}

function editFile(file: MWSFile) {
	showEditFile.value = true;
	currentFile.value = clone(file);
	changeFileProgress.value = undefined;
	cancelFileUpload.value = undefined;
	disableChangeFile.value = false;

	canSubmitFile.value = true;
	if (changeFile.value) {
		changeFile.value = undefined;
	}
}

async function saveEditFile(error) {
	try {
		const file = currentFile.value;
		if (file) {
			canSubmitFile.value = false;
			const formData = new FormData();

			if (!config.presignedUpload && changeFile.value) {
				formData.append('change_file', changeFile.value);
			}

			for (const [key, value] of Object.entries(file)) {
				formData.append(key, value !== null ? value : '');
			}

			await patchRequest(`files/${file.id}`, formData, {
				headers: { 'Content-Type': 'multipart/form-data' }
			});

			for (const f of files.value) {
				if (f.id === file.id) {
					Object.assign(f, file);
				}
			}

			showEditFile.value = false;
		}
	} catch (e) {
		if (!(e instanceof CanceledError)) {
			error(e);
		}
	}
	canSubmitFile.value = true;
}

async function cancelEditFile() {
	if (cancelFileUpload.value) {
		cancelFileUpload.value();
	}
}

function fileUploaded(file: MWSFile) {
	if (mod.value.used_storage) {
		mod.value.used_storage += file.size;
	}
	updateHasDownload();
}

function fileDeleted(file: MWSFile) {
	if (mod.value.download_id === file.id) {
		setPrimaryDownload();
	}

	updateHasDownload();
	if (mod.value.used_storage && file.id) {
		mod.value.used_storage -= file.size;
	}

	if (files.value.length === 0) {
		filesPage.value = 1;
		refreshFiles();
	}
}

function editLink(link: Link) {
	showEditLink.value = true;
	currentLink.value = clone(link);
}

async function deleteLink(link: Link) {
	if (link.id) {
		await deleteRequest(`links/${link.id}`);
	}

	remove(links.value, link);
	updateHasDownload();

	if (links.value.length === 0) {
		linksPage.value = 1;
		refreshLinks();
	}
}

function createNewLink() {
	editLink(clone({
		id: -1,
		user_id: user!.id,
		mod_id: mod.value.id,
		name: '',
		desc: '',
		url: '',
		label: '',
		version: ''
	}));
}

async function saveEditLink(error) {
	let link = currentLink.value;

	try {
		if (link) {
			if (link.id === -1) {
				if (mod.value.id) {
					const newLink = await postRequest<Link>(`mods/${mod.value.id}/links`, link);
					links.value.push(newLink);
				} else {
					link.id = 0;
					links.value.push(clone(link));
				}
			} else if (link.id) {
				link = await patchRequest(`links/${link.id}`, link);
			}

			for (const l of links.value) {
				if (l.id === link?.id) {
					Object.assign(l, link);
				}
			}
			updateHasDownload();

			currentLink.value = undefined;
		}

		showEditLink.value = false;
	} catch (e) {
		error(e);
	}
}
</script>
