<template>
	<m-flex column class="items-center text-center">
		<h2>{{ $t('downloading_file') }}</h2>
		<h3>{{ file.type }} - {{ friendlySize(file.size) }}</h3>
		<h3>{{ $t('downloading_file_should') }}</h3>
		<m-flex>
			<m-button :to="`/mod/${mod.id}`"><i-mdi-arrow-left/> {{ $t('return_to_mod') }}</m-button>
			<a ref="download" download :href="file.download_url">
				<m-button><i-mdi-download/> {{ $t('downloading_file_force') }}</m-button>
			</a>
			<m-button
				v-if="mod.instructs_template || mod.instructions"
				:to="`/mod/${mod.id}?tab=instructions`"
				color="warning"
			>
				<i-mdi-help/> {{ $t('downloading_file_help') }}
			</m-button>
		</m-flex>
	</m-flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { File, Mod } from '~/types/models';

const { t } = useI18n();

const { mod } = defineProps<{
	mod: Mod;
}>();

const download = ref<HTMLAnchorElement>();

const { data: file } = await useResource<File>('file', 'files');

if (!file.value) {
	throw createError({ statusCode: 404, statusMessage: t('file_doesnt_exist') });
}

// Annoyingly we needed to wrap the button in a different anchor element since ref doesn't always include the element in the DOM
// Basically if it's a componnent, it will be a component ref which doesn't seem to include the actual element!
// Otherwise if it's a simple element, it will be the element itself.
watch(download, () => {
	if (download.value) {
		download.value.click();
		registerDownload('file', file.value);
	}
});
</script>
