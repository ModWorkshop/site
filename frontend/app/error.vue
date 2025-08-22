<template>
	<NuxtLayout>
		<m-flex column class="items-center mt-1">
			<h1 class="mx-auto">{{ $t('error') }} {{ error.statusCode }}</h1>
			<h3>{{ error.statusMessage }}</h3>
			<h4 v-if="error.statusMessage != error.message">({{ error.message }})</h4>
			<pre v-if="dev" class="whitespace-pre">{{ error.stack }}</pre>
			<m-button @click="clearError({ redirect: '/' })">{{ $t('back_to_home') }}</m-button>
		</m-flex>
	</NuxtLayout>
</template>

<script setup lang="ts">
import type { NuxtError } from '#app';
import { useStore } from './store';

defineProps<{
	error: NuxtError;
}>();

const store = useStore();
const dev = process.env.NODE_ENV === 'development';

useHeadSafe({
	titleTemplate: titleChunk => {
		return titleChunk ? `${titleChunk} - ModWorkshop` : 'ModWorkshop';
	},
	htmlAttrs: {
		class: computed(() => `${store.theme} ${store.colorScheme}-scheme`)
	},
	title: undefined
});
</script>
