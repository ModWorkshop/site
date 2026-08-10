<template>
	<m-flex>
		<m-button
			v-if="download && type == 'file'"
			:class="classes"
			:to="!static ? downloadUrl : undefined"
			download
			@click="!static && registerDownload('file', download);"
		>
			<i-mdi-download/>
			{{ $t('download') }}
			<template v-if="!small">
				<span v-if="!small" style="text-transform:uppercase;">{{ (download as any).type }}</span> ({{ friendlySize((download as any).size) }})
			</template>
		</m-button>
		<m-dropdown v-else-if="download && type == 'link'" class="flex-1 flex">
			<m-button :class="classes" @click="!static && registerDownload('link', download);">
				<i-mdi-download/> {{ $t('show_download_link') }}
			</m-button>
			<template #content>
				<div class="word-break p-2" style="width: 250px;">
					<b>
						{{ $t('show_download_link_warn') }}
					</b>
					<br>
					<br>
					<a :href="(download as any).url">{{ (download as any).url }}</a>
				</div>
			</template>
		</m-dropdown>
		<slot/>
	</m-flex>
	<m-flex v-if="primaryModManager && download && type == 'file'" column>
		<m-flex class="w-full">
			<m-button
				:class="classes"
				:to="!static ? getManagerDownloadUrl(primaryModManager, download as File) : undefined"
			>
				<i-mdi-progress-wrench/> {{ small ? $t('install') : $t('install_with', { modManager: primaryModManager.name }) }}
			</m-button>
			<m-dropdown v-if="!small">
				<m-button :class="classes" style="height: stretch;">
					<i-mdi-chevron-down/>
				</m-button>
				<template #content>
					<m-dropdown-item v-for="manager of mod.mod_managers" :key="manager.id" @click="() => setModManager(manager)">{{ manager.name }}</m-dropdown-item>
				</template>
			</m-dropdown>
		</m-flex>
		<small v-if="!small && primaryModManager.site_url">
			<NuxtLink :to="primaryModManager.site_url"> {{ $t('mod_manager_not_installed', { modManager: primaryModManager.name }) }} </NuxtLink>
		</small>
	</m-flex>
</template>

<script setup lang="ts">
import type { Mod, ModManager, File, Link } from '~/types/models';

const props = defineProps<{
	mod: Mod;
	download?: File | Link;
	small?: boolean;
	static?: boolean;
	type?: 'link' | 'file';
}>();

const chosenModManager = useCookie<number>(props.mod.game_id + '-mod-manager', { decode: parseInt, expires: longExpiration() });

const managers = computed(() => props.mod.mod_managers ?? []);

const classes = computed(() => ({
	'download-button': true,
	'flex-1': true,
	'download-button-small': props.small
}));

// const downloadUrl = computed(() => `/mod/${props.mod.id}/download/${props.download!.id}`);
const downloadUrl = computed(() => (props.download as File).download_url);

const primaryModManager = computed(() => {
	if (props.mod.disable_mod_managers || props.mod.category?.disable_mod_managers) {
		return null;
	}

	const chosen = chosenModManager.value;
	const defaultManager = props.mod.game?.default_mod_manager_id;

	return managers.value?.find(manager => manager.id === chosen)
	  ?? managers.value?.find(manager => manager.id === defaultManager)
	  ?? managers.value[0];
});

function setModManager(manager: ModManager) {
	chosenModManager.value = manager.id;
}

function getManagerDownloadUrl(manager: ModManager, file: File) {
	const replace = {
		':mod_id': props.mod.id,
		':file_id': file.id,
		':manager_name': manager.name,
		':game_id': props.mod.game?.id,
		':game_short_name': props.mod.game?.short_name
	};
	return manager.download_url.replaceAll(/:\w+_?\w*/g, str => replace[str]);
}
</script>

<style>
.download-button {
	font-size: 1.15rem;
	padding: 1rem !important;
	text-align: center;
}

.download-button-small {
	font-size: 1rem;
	padding: 0.75rem !important;
}
</style>
