<template>
	<m-flex>
		<m-button
			v-if="download && type == 'file'"
			class="large-button flex-1"
			:to="!static ? downloadUrl : undefined"
			download
			@click="!static && registerDownload('file', download);"
		>
			<i-mdi-download/> {{ $t('download') }}
			<span style="text-transform:uppercase;">{{ (download as any).type }}</span> ({{ friendlySize((download as any).size) }})
		</m-button>
		<m-dropdown v-else-if="download && type == 'link'" class="flex-1 flex">
			<m-button class="large-button flex-1" @click="!static && registerDownload('link', download);">
				<i-mdi-download/> {{ $t('show_download_link') }}
			</m-button>
			<template #content>
				<div class="word-break p-2" style="width: 250px;">
					{{ $t('show_download_link_warn') }}
					<br>
					<a class="text-lg font-bold" :href="(download as any).url">{{ (download as any).url }}</a>
				</div>
			</template>
		</m-dropdown>
		<slot/>
	</m-flex>
	<m-flex v-if="primaryModManager && download && type == 'file'">
		<m-flex column class="w-full">
			<m-button class="large-button" style="flex: 6;" :to="!static ? getManagerDownloadUrl(primaryModManager, download as File) : undefined">
				<i-mdi-progress-wrench/> {{ $t('install_with', { modManager: primaryModManager.name }) }}
			</m-button>
			<small v-if="primaryModManager.site_url">
				<NuxtLink :to="primaryModManager.site_url"> {{ $t('mod_manager_not_installed', { modManager: primaryModManager.name }) }} </NuxtLink>
			</small>
		</m-flex>

		<m-dropdown class="self-start">
			<m-button class="large-button text-center h-full">
				<i-mdi-chevron-down/>
			</m-button>
			<template #content>
				<m-dropdown-item v-for="manager of mod.mod_managers" :key="manager.id" @click="() => setModManager(manager)">{{ manager.name }}</m-dropdown-item>
			</template>
		</m-dropdown>
	</m-flex>
</template>

<script setup lang="ts">
import type { Mod, ModManager, File, Link } from '~/types/models';

const props = defineProps<{
	mod: Mod;
	download?: File | Link;
	static?: boolean;
	type?: 'link' | 'file';
}>();

const chosenModManager = useCookie<number>(props.mod.game_id + '-mod-manager', { decode: parseInt, expires: longExpiration() });

const managers = computed(() => props.mod.mod_managers ?? []);

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
