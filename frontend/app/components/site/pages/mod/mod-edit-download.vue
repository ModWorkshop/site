<template>
	<tr>
		<td>
			<m-flex>
				<input
					:checked="(download.id === mod.download_id && mod.download_type == 'file') ? true : undefined"
					type="radio"
					:disabled="!download.size"
					@change="$emit('setPrimaryDownload', type, download)"
				>
			</m-flex>
		</td>
		<td class="collapse-col">
			<m-img v-if="image" url-prefix="mods/images" :src="image.file" loading="lazy" width="48" height="48"/>
			<mod-file-version-type v-else-if="download.version_type" :file="download"/>
		</td>
		<td>
			<m-flex class="items-center" gap="3">
				<div class="text-ellipsis overflow-hidden" style="max-width: 120px;" :title="download.version">
					{{ download.version || 'N/A' }}
				</div>
			</m-flex>
		</td>
		<td class="whitespace-pre-line wrap-anywhere" >
			<m-flex class="items-center" style="min-width: 80px; max-width: 200px;" wrap>
				<template v-if="download.type">
					{{ download.name + '.' + download.type }}
				</template>
				<template v-else>
					{{ download.name }}
				</template>
			</m-flex>
		</td>
		<td v-if="download.size != undefined">
			<template v-if="download.actualFile">
				{{ friendlySize(download.actualFile.size) }}
			</template>
			<template v-else>
				{{ download.size ? friendlySize(download.size) : $t('file_missing') }}
			</template>
		</td>
		<td v-else-if="download.url">
			{{ download.url }}
		</td>
		<td>
			<div style="width: 100px;">
				<span v-if="paused">{{ $t('file_waiting') }}</span>
				<m-flex v-else-if="download.progress" column>
					<m-progress
						:current="progress?.progress"
						:height="8"
						:show-text="false"
					/>
					<small class="whitespace-pre-line">
						{{ $t('uploading_table', { current, total, speed, time }) }}
					</small>
				</m-flex>

				<m-time v-else-if="download.created_at" :datetime="download.created_at" relative relative-time-style="narrow"/>
				<span v-else>{{ $t('waiting') }}</span>
			</div>
		</td>
		<td>
			<m-flex class="items-end">
				<m-flex class="ml-auto">
					<m-button v-if="download.cancel" color="danger" @click.prevent="$emit('cancel', download)"><i-mdi-stop/></m-button>
					<m-button v-else color="danger" @click.prevent="$emit('remove', download)"><i-mdi-delete/></m-button>
					<m-button @click.prevent="$emit('edit', download)"><i-mdi-cog/></m-button>
				</m-flex>
			</m-flex>
		</td>
	</tr>
</template>

<script setup lang="ts">
import type { UploadSimpleFile } from '~/types/core';
import type { Link, Mod } from '~/types/models';

const { mod, download } = defineProps<{
	download;
	type: 'file' | 'link';
	paused?: boolean;
	mod: Mod;
}>();

defineEmits<{
	(e: 'edit', download: UploadSimpleFile & Link): void;
	(e: 'remove', download: UploadSimpleFile & Link): void;
	(e: 'cancel', download: UploadSimpleFile): void;
	(e: 'setPrimaryDownload', type: 'file' | 'link', download: UploadSimpleFile & Link): void;
}>();

const image = computed(() => mod.images?.find(image => image.id === download.image_id));
const progress = computed(() => download.progress);
const { locale } = useI18n();
const durationFormat = computed(() => new Intl.DurationFormat(locale.value, { style: 'narrow', secondsDisplay: 'always' }));

const time = computed(() => huamnizeDuration(progress.value?.estimated ?? 0, durationFormat.value));
const speed = computed(() => friendlySize(progress.value?.rate ?? 0));
const current = computed(() => friendlySize(progress.value?.loaded ?? 0));
const total = computed(() => friendlySize(progress.value?.total ?? 0));
</script>
