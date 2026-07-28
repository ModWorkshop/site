<template>
	<tr class="hover:cursor-pointer download-tr" @click="showDetails = !showDetails">
		<td :class="{ 'collapse-col': !image }">
			<m-img v-if="image" url-prefix="mods/images" :src="image.file" loading="lazy" width="48" height="48"/>
		</td>
		<td>
			<div class="text-ellipsis overflow-hidden" style="width: 100px;" :title="file.version">
				{{ file.version || 'N/A' }}
			</div>
		</td>
		<td class="whitespace-pre-line wrap-anywhere">
			<m-flex class="items-center">
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
		<td v-if="file.size">
			{{ friendlySize(file.size) }}
		</td>
		<td v-if="file.updated_at">
			<m-time :datetime="file.updated_at" relative relative-time-style="narrow"/>
		</td>
		<td>
			<m-flex class="items-end">
				<m-flex class="ml-auto">
					<mod-download-buttons :mod="mod" :download="file" :type="type" small/>
				</m-flex>
			</m-flex>
		</td>
	</tr>
	<tr :class="{hidden: !showDetails, 'download-tr': showDetails}">
		<td colspan="10">
			<m-flex class="p-3" column gap="2">
				<md-content v-if="file.desc" :text="file.desc" :padding="1" style="max-height: 250px; overflow-y: auto;"/>
				<m-flex class="items-center" wrap>
					<i18n-t keypath="updated_by_user_time_ago" scope="global">
						<template #time>
							<m-time :datetime="file.updated_at" relative/>
						</template>
						<template #user>
							<a-user :user="file.user" :avatar="false"/>
						</template>
					</i18n-t>
				</m-flex>
				{{ $t(type + '_id') }}: {{ file.id }}
			</m-flex>
		</td>
	</tr>
</template>

<script setup lang="ts">
import type { File, Link, Mod } from '~/types/models';

const props = defineProps<{
	file: File & Link;
	type: 'file' | 'link';
	mod: Mod;
}>();

const i18n = useI18n();
const showDetails = ref(false);
const locale = computed(() => i18n.locale.value);
const image = computed(() => props.mod.images?.find(image => image.id === props.file.image_id));
</script>

<style>
.collapse-col {
	width: 0%;
}

.downloads-table tr:nth-child(4n of tr) td, .downloads-table tr:nth-child(4n+3) td {
	background-color: var(--alt-table-even-color) !important;
}

.downloads-table tr:nth-child(4n+1 of tr) td, .downloads-table tr:nth-child(4n+2 of tr) td {
	background-color: var(--alt-table-odd-color) !important;
}
</style>
