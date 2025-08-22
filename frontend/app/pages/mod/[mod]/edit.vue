<template>
	<edit-mod-page v-model:mod="mod"/>
</template>

<script setup lang="ts">
import type { Mod } from '~/types/models';

const mod = defineModel<Mod>('mod', { required: true });

definePageMeta({
	middleware: 'users-only'
});
</script>
