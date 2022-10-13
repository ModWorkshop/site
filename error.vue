<template>
	<NuxtLayout>
		<Html :class="store.theme === 'light' ? 'light' : 'dark'"/>
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
import { Settings } from 'luxon';
import { useStore } from './store';
import { User } from './types/models';

const props = defineProps({
	error: Object
});

const store = useStore();

if (props.error.data) {
	const data: { user: User, theme?: 'light'|'dark', scheme: string } = JSON.parse(props.error.data);
	store.user = data.user;
	store.savedTheme = data.theme;
	store.colorScheme = data.scheme;
}

const dev = process.env.NODE_ENV === 'development';

useHead({
	titleTemplate: (titleChunk) => {
		return titleChunk ? `${titleChunk} - ModWorkshop` : 'ModWorkshop';
    },
	bodyAttrs: {
		style: computed(() => `
			--primary-color: var(--mws-${store.colorScheme});
			--primary-hover-color: var(--mws-${store.colorScheme}-hover); 
			--primary-color-text: var(--mws-${store.colorScheme}-text)` 
		)
	},
	title: undefined,
});

Settings.defaultLocale = 'en-US';
</script>