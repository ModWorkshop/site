<template>
	<div>
		<NuxtLayout>
			<NuxtPage/>
			<NuxtLoadingIndicator/>
		</NuxtLayout>
	</div>
</template>

<script setup lang="ts">
import { Settings } from 'luxon';

useHead({
	titleTemplate: (titleChunk) => {
		return titleChunk ? `${titleChunk} - ModWorkshop` : 'ModWorkshop';
    },
	title: undefined,
});

Settings.defaultLocale = 'en-US';
</script>

<style>
.markdown h1, .markdown h2 {
	border-bottom: 1px solid #424242;
	padding-bottom: .3em;
}

h1, h2, h3, h4, h5, h6 {
	margin-top: 0;
	margin-bottom: .5rem;
}

/* p {
    margin-top: 0;
    margin-bottom: 1rem;
} */

dl, ol, ul {
  margin-top: 0;
  margin-bottom: 1rem;
}
</style>