<template>
	<m-flex class="w-full mb-4" wrap gap="3">
		<span class="h2 flex-1">{{ $t('downloads') }}</span>
		<m-button @click="setPrimaryDownload()"><i-mdi-close/> {{ $t('clear_primary_download') }}</m-button>
	</m-flex>

	<edit-mod-file-uploader
		v-model="files"
		v-model:mod="mod"
		@update-has-download="updateHasDownload"
		@set-primary-download="setPrimaryDownload"
	/>

	<m-flex column>
		<m-flex class="items-center">
			<label>{{ $t('links') }}</label>
			<m-button v-if="links" class="ml-auto" @click="createNewLink">
				<i-mdi-plus-thick/>
			</m-button>
		</m-flex>
		<m-table alt-background>
			<template #head>
				<th/>
				<th>{{ $t('version') }}</th>
				<th>{{ $t('name') }}</th>
				<th>{{ $t('url') }}</th>
				<th>{{ $t('date') }}</th>
				<th/>
			</template>
			<template #body>
				<mod-edit-download
					v-for="link of links"
					:key="link.id"
					:download="link"
					:mod="mod"
					type="link"
					@remove="deleteLink"
					@edit="editLink"
					@set-primary-download="setPrimaryDownload"
				/>
			</template>
		</m-table>
		<m-pagination v-model="linksPage" :per-page="10" :total="asyncLinks?.meta.total"/>
	</m-flex>

	<span class="h2 my-4">{{ $t('updates') }}</span>

	<md-editor v-model="mod.changelog" :label="$t('changelog')" rows="12"/>
	<m-input v-model="mod.repo_url" :label="$t('repo_url')" type="url"/>
	<m-input v-model="mod.custom_version" :label="$t('version')" :desc="$t('custom_version_help')"/>
	<m-input v-if="canModerate && !light" v-model="mod.allowed_storage" type="number" max="1000" :label="$t('allowed_storage')" :desc="$t('allowed_storage_help')"/>
	<m-input
		v-model="mod.files_are_versions"
		:label="$t('files_are_versions')"
		:desc="$t('files_are_versions_desc')"
		type="checkbox"
	/>
	<m-input v-model="mod.disable_mod_managers" :label="$t('disable_mod_managers')" :desc="$t('disable_mod_managers_desc')" type="checkbox"/>

	<m-form-modal
		v-if="currentLink"
		v-model="showEditLink"
		:title="!currentLink.id ? $t('new_link') : $t('edit_link')"
		:close-on-click-outside="false"
		size="lg" @submit="saveEditLink"
	>
		<m-input v-model="currentLink.url" type="url" required :label="$t('url')"/>
		<m-flex>
			<m-input v-model="currentLink.name" required :label="$t('name')"/>
			<m-input v-model="currentLink.version" :label="$t('version')"/>
		</m-flex>
		<md-editor v-model="currentLink.desc" rows="8" :label="$t('description')"/>
		<m-flex>
			<m-input v-model="currentLink.label" :label="$t('label')"/>
			<m-input v-model="currentLink.display_order" :label="$t('order')"/>
		</m-flex>
		<m-select v-model="currentLink.image_id" :label="$t('thumbnail')" :options="mod.images" :filterable="false" clearable null-clear>
			<template #any-option="{ option }">
				<m-img style="width: 100px; height: 100px; object-fit: contain" loading="lazy" url-prefix="mods/images" :src="option.file" />
			</template>
		</m-select>
	</m-form-modal>
</template>

<script setup lang="ts">
import type { File as MWSFile, Link, Mod } from '~/types/models';
import { useStore } from '~/store';
import { remove } from '@antfu/utils';

const { hasPermission, user } = useStore();

defineProps<{
	light?: boolean;
}>();

const mod = defineModel<Mod>({ required: true });
const canModerate = computed(() => hasPermission('manage-mods', mod.value.game));

// Edit link modal stuff
const showEditLink = ref(false);
const currentLink = ref<Link>();

const linksPage = ref(1);

const initialMod = inject<Mod>('initialMod');

const { data: asyncLinks, refresh: refreshLinks } = await useFetchMany(`mods/${mod.value.id}/links`, {
	query: {
		limit: 20,
		page: linksPage
	},
	immediate: !!mod.value.id
});

const files = ref<MWSFile[]>([]);
const linksRef = ref<Link[]>([]);

const links = computed<Link[]>(() => asyncLinks.value?.data ?? linksRef.value);

// Handle mod submit
watch(() => mod.value.id, async () => {
	for (const link of links.value) {
		if (!link.id) {
			const newLink = await postRequest<Link>(`mods/${mod.value.id}/links`, link);
			Object.assign(link, newLink);
		}
	}
});

function updateHasDownload() {
	mod.value.files_count = files.value.length ?? 0;
	mod.value.links_count = links.value.length ?? 0;
	mod.value.has_download = (mod.value.files_count > 0) || (mod.value.links_count > 0) || false;

	if (initialMod) {
		initialMod.has_download = mod.value.has_download;
	}

	if (Math.abs(mod.value.files_count - mod.value.links_count) === 1) {
		mod.value.download = files.value[0] ?? links.value[0];
	}
}

function setPrimaryDownload(type?: 'file' | 'link', download?: MWSFile | Link) {
	mod.value.download_type = type ?? null;
	mod.value.download_id = (download && download.id) ?? null;
	mod.value.download = download;
}

function editLink(link: Link) {
	showEditLink.value = true;
	currentLink.value = { ...link };
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
	editLink({
		id: 0,
		user_id: user!.id,
		mod_id: mod.value.id,
		name: '',
		desc: '',
		url: '',
		label: '',
		version: '',
		display_order: 0
	});
}

async function saveEditLink(error) {
	let link = currentLink.value;

	try {
		if (link) {
			if (!link.id) {
				if (mod.value.id) {
					const newLink = await postRequest<Link>(`mods/${mod.value.id}/links`, link);
					links.value.push(newLink);
				} else {
					links.value.push({ ...link });
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
