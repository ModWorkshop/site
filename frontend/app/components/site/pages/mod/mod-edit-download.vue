<template>
	<tr>
		<td>
			<input
				:checked="(download.id === mod.download_id && mod.download_type == 'file') ? true : undefined"
				type="radio"
				:disabled="!download.size"
				@change="$emit('setPrimaryDownload', type, download)"
			>
			<m-img v-if="image" url-prefix="mods/images" :src="image.file" loading="lazy" width="48" height="48"/>
		</td>
		<td>
			<div class="text-ellipsis overflow-hidden" style="max-width: 120px;" :title="download.version">
				{{ download.version || 'N/A' }}
			</div>
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
			<div style="max-width: 270px; min-width: 200px;">
				<span v-if="paused">{{ $t('file_waiting') }}</span>
				<m-uploader-progress v-else-if="download.progress" :progress="download.progress" type="circle"/>
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

const props = defineProps<{
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

const image = computed(() => props.mod.images?.find(image => image.id === props.download.image_id));
</script>
