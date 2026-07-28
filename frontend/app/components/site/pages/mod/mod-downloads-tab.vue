<template>
	<m-flex column gap="3">
		<m-flex column>
			<m-flex column class="ml-auto">
				<m-flex class="items-center">
					{{ $t('mod_manager') }}: <m-select v-model="chosenModManager" class="w-50" :options="mod.mod_managers"/>
				</m-flex>
				<small v-if="primaryModManager?.site_url" class="ml-auto">
					<NuxtLink :to="primaryModManager.site_url"> {{ $t('mod_manager_not_installed', { modManager: primaryModManager.name }) }} </NuxtLink>
				</small>
			</m-flex>
		</m-flex>
		<m-list v-if="files?.meta.total" v-model:page="filesPage" :limit="20" :title="$t('files')" :items="files" :loading="loadingFiles">
			<template #items="{ items }">
				<m-table alt-background class="downloads-table">
					<template #head>
						<th/>
						<th style="width: 10%;">{{ $t('version') }}</th>
						<th style="width: 25%;">{{ $t('name') }}</th>
						<th style="width: 10%;">{{ $t('downloads') }}</th>
						<th style="width: 10%;">{{ $t('file_size') }}</th>
						<th style="width: 10%;">{{ $t('date') }}</th>
						<th style="width: 25%;"/>
					</template>
					<template #body>
						<mod-download v-for="file of items.data" :key="file.id" :file="file" :mod="mod" type="file"/>
					</template>
				</m-table>
			</template>
		</m-list>

		<m-list v-if="links?.meta.total" v-model:page="linksPage" :limit="20" :title="$t('links')" :items="links" :loading="loadingLinks">
			<template #items="{ items }">
				<m-table alt-background class="downloads-table">
					<template #head>
						<th/>
						<th style="width: 10%;">{{ $t('version') }}</th>
						<th style="width: 25%;">{{ $t('name') }}</th>
						<th style="width: 10%;">{{ $t('downloads') }}</th>
						<th style="width: 10%;">{{ $t('date') }}</th>
						<th style="width: 25%;"/>
					</template>
					<template #body>
						<mod-download v-for="link of items.data" :key="link.id" :file="link" :mod="mod" type="link"/>
					</template>
				</m-table>
			</template>
		</m-list>
	</m-flex>
</template>

<script setup lang="ts">
import type { Mod } from '~/types/models';

const props = defineProps<{
	mod: Mod;
}>();

const filesPage = ref(1);
const linksPage = ref(1);

const chosenModManager = useCookie<number>(props.mod.game_id + '-mod-manager', { decode: parseInt, expires: longExpiration() });
const managers = computed(() => props.mod.mod_managers ?? []);
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

const { data: files, loading: loadingFiles } = await useFetchMany(`mods/${props.mod.id}/files`, {
	query: { page: filesPage, limit: 20 },
	lazy: true
});

const { data: links, loading: loadingLinks } = await useFetchMany(`mods/${props.mod.id}/links`, {
	query: { page: linksPage, limit: 20 },
	lazy: true
});
</script>
