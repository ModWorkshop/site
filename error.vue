<template>
	<NuxtLayout>
        <flex column class="items-center">
            <h1 class="mx-auto">Error</h1>
            <h3>{{error.statusCode}}</h3>
            <h3>{{error.statusMessage}}</h3>
            <div v-if="dev" class="mt-4" v-html="error.description"/>
        </flex>
        <NuxtLoadingIndicator/>
  </NuxtLayout>
</template>

<script setup>
import { Settings } from 'luxon';

const dev = process.env.NODE_ENV === 'development';

defineProps({
  error: Object
});

Settings.defaultLocale = 'en-US';
</script>