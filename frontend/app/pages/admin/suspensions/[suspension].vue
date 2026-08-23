<template>
	<simple-resource-form
		v-model="suspension"
		url="suspensions"
		:game="game"
		:redirect-to="redirectTo"
	>
		<Title>
			{{ $t('suspension_mod', { mod: suspension.mod.name}) }}
		</Title>
		<m-input v-model="suspension.reason" :label="$t('reason')" type="textarea" rows="10"/>
	</simple-resource-form>
</template>

<script setup lang="ts">
import type { Suspension, Game } from '~/types/models';

const props = defineProps<{
	game: Game;
}>();

useNeedsPermission('manage-mods', props.game);

const redirectTo = computed(() => getAdminUrl('suspensions', props.game));
const { data: suspension } = await useEditResource<Suspension>('suspension', 'suspensions');
</script>
