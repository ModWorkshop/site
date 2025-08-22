<template>
	<m-flex gap="3" column>
		<h2>{{ $t('home_page') }}</h2>

		<m-alert :desc="$t('game_admin_page_desc')"/>

		<admin-glance :game="game"/>
	</m-flex>
</template>

<script setup lang="ts">
import type { Game } from '~/types/models';

defineProps<{
	game: Game;
}>();
</script>
