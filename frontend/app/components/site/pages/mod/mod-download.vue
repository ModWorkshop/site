<template>
	<template v-if="table">
		<tr class="hover:cursor-pointer download-tr" @click="showDetails = !showDetails">
			<td>
				<m-img v-if="image" url-prefix="mods/images" :src="image.file" loading="lazy" width="48" height="48"/>
				<mod-file-version-type v-else-if="file.version_type" :file="file"/>
			</td>
			<td>
				<div class="whitespace-pre-line wrap-anywhere" style="max-width: 200px;" :title="file.version">
					{{ file.version || 'N/A' }}
				</div>
			</td>
			<td class="whitespace-pre-line wrap-anywhere" >
				<m-flex class="items-center" style="min-width: 100px; max-width: 200px;" wrap>
					<template v-if="file.type">
						{{ file.name + '.' + file.type }}
					</template>
					<template v-else>
						{{ file.name }}
					</template>
					<m-tag v-if="file.label" class="whitespace-pre">{{ file.label }}</m-tag>
				</m-flex>
			</td>
			<td>
				{{ friendlyNumber(locale, file.downloads) }}
			</td>
			<td v-if="type == 'file'">
				{{ friendlySize(file.size) }}
			</td>
			<td v-if="file.created_at">
				<m-time :datetime="file.created_at" relative relative-time-style="narrow"/>
			</td>
			<td>
				<m-flex class="ml-auto justify-end" @click.stop>
					<mod-download-buttons :mod="mod" :download="file" :type="type" small/>
				</m-flex>
			</td>
		</tr>
		<tr :class="{hidden: !showDetails, 'download-tr': showDetails}">
			<td colspan="10">
				<mod-download-details :file="file" :type="type"/>
			</td>
		</tr>
	</template>
	<m-flex v-else class="list-button" column>
		<m-flex class="flex-1 items-center hover:cursor-pointer" gap="3" @click="showDetails = !showDetails">
			<m-img v-if="image" url-prefix="mods/images" class="mb-auto" :src="image.file" loading="lazy" width="32" height="32"/>
			<mod-file-version-type v-else-if="file.version_type" class="mb-auto" :file="file"/>

			<m-flex grow column style="flex: 1;" gap="2">
				<m-flex class="items-center whitespace-pre-line wrap-anywhere">
					<template v-if="file.type">
						{{ file.name + '.' + file.type }}
					</template>
					<template v-else>
						{{ file.name }}
					</template>
					<m-tag v-if="file.label" class="whitespace-pre">{{ file.label }}</m-tag>
				</m-flex>
				<m-flex v-if="file.version" :title="$t('version')">
					<i-mdi-tag/> <span class="wrap-anywhere">{{ file.version }}</span>
				</m-flex>
				<m-flex :title="$t('downloads')" class="items-center">
					<i-mdi-download/> <span :title="file.downloads.toString()">{{ friendlyNumber(locale, file.downloads) }}</span>
				</m-flex>
				<span>
					<i-mdi-clock/> <m-time :datetime="file.updated_at" relative relative-time-style="narrow"/>
				</span>
			</m-flex>
			<m-flex @click.stop>
				<mod-download-buttons :mod="mod" :download="file" :type="type" small/>
			</m-flex>
		</m-flex>
		<mod-download-details :class="{ hidden: !showDetails }" :file="file" :type="type"/>
	</m-flex>
</template>

<script setup lang="ts">
import type { File, Link, Mod } from '~/types/models';

const { file, mod } = defineProps<{
	file: File & Link;
	type: 'file' | 'link';
	table?: boolean;
	mod: Mod;
}>();

const i18n = useI18n();
const showDetails = ref(false);
const locale = computed(() => i18n.locale.value);
const image = computed(() => mod.images?.find(image => image.id === file.image_id));

</script>

<style>
.downloads-table tr:nth-child(4n of tr) td, .downloads-table tr:nth-child(4n+3) td {
	background-color: var(--alt-table-even-color) !important;
}

.downloads-table tr:nth-child(4n+1 of tr) td, .downloads-table tr:nth-child(4n+2 of tr) td {
	background-color: var(--alt-table-odd-color) !important;
}
</style>
