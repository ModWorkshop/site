<template>
	<NuxtLayout>
		<flex column class="items-center">
			<h1 class="mx-auto">{{$t('error')}}</h1>
			<h3>{{error.statusCode}}</h3>
			<h3>{{error.statusMessage}}</h3>
			<a-button @click="clearError({ redirect: '/' })">{{$t('back_to_home')}}</a-button>
			<div v-if="dev && error.description" class="mt-4" v-html="error.description"/>
		</flex>
		<NuxtLoadingIndicator/>
	</NuxtLayout>
</template>

<script setup>
import { Settings } from 'luxon';

const dev = process.env.NODE_ENV === 'development';

useHead({
	titleTemplate: (titleChunk) => {
		return titleChunk ? `${titleChunk} - ModWorkshop` : 'ModWorkshop';
    },
	htmlAttrs: {
        class: 'dark'
    },
	title: undefined,
});

defineProps({
	error: Object
});

Settings.defaultLocale = 'en-US';
</script>