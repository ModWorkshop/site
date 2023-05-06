<template>
	<NuxtLayout>
		<flex column class="items-center mt-1">
			<h1 class="mx-auto">{{$t('error')}}</h1>
			<h3>{{error.statusCode}}</h3>
			<h3>{{error.statusMessage}}</h3>
			<a-button @click="clearError({ redirect: '/' })">{{$t('back_to_home')}}</a-button>
			<div v-if="dev && error.description" class="mt-4" v-html="error.description"/>
		</flex>
	</NuxtLayout>
</template>

<script setup lang="ts">
import { useStore } from './store';

defineProps<{
	error: {
		statusCode: number,
		statusMessage: string
		description?: string,
	}
}>();

const store = useStore();

const dev = process.env.NODE_ENV === 'development';

useHead({
	titleTemplate: (titleChunk) => {
		return titleChunk ? `${titleChunk} - ModWorkshop` : 'ModWorkshop';
    },
	htmlAttrs: {
		class: computed(() => `${store.theme === 'light' ? 'light' : 'dark'} ${store.colorScheme}-scheme`)
	},
	title: undefined,
});
</script>