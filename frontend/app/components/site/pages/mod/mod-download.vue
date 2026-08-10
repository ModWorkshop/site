<template>
	<template v-if="table">
		<tr class="hover:cursor-pointer download-tr" @click="showDetails = !showDetails">
			<td :class="{ 'collapse-col': !image }">
				<m-img v-if="image" url-prefix="mods/images" :src="image.file" loading="lazy" width="48" height="48"/>
			</td>
			<td>
				<div class="text-ellipsis overflow-hidden" style="max-width: 120px;" :title="file.version">
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
			<td v-if="file.size">
				{{ friendlySize(file.size) }}
			</td>
			<td v-if="file.updated_at">
				<m-time :datetime="file.updated_at" relative relative-time-style="narrow"/>
			</td>
			<td>
				<m-flex class="ml-auto justify-end" wrap @click.stop>
					<mod-download-buttons :mod="mod" :download="file" :type="type" small/>
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
	<m-flex v-else class="list-button" column>
		<m-flex wrap class="flex-1 items-center hover:cursor-pointer" gap="3" @click="showDetails = !showDetails">
			<m-img v-if="image" url-prefix="mods/images" :src="image.file" loading="lazy" width="48" height="48"/>
			<m-img v-else src="file-download.webp" is-asset width="48" height="48"/>
			<m-flex grow column style="flex: 1;" gap="2">
				<m-flex class="items-center whitespace-pre-line" wrap style="max-width: 120px;">
					<m-tag v-if="file.label">{{ file.label }}</m-tag>
					<strong v-if="file.name" class="items-center">{{ file.name }}</strong>
					<strong v-else class="items-center">{{ $t(`file_type_${type}`) }}</strong>
				</m-flex>
				<span v-if="file.version" :title="$t('version')">
					<i-mdi-tag/> {{ file.version }}
				</span>
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
		<m-flex :class="{ hidden: !showDetails, 'p-3': true }" column gap="2">
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
	</m-flex>
</template>

<script setup lang="ts">
import type { File, Link, Mod } from '~/types/models';

const props = defineProps<{
	file: File & Link;
	type: 'file' | 'link';
	table?: boolean;
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
