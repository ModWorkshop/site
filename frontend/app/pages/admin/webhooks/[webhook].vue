<template>
	<simple-resource-form v-model="webhook" url="webhooks" :game="game" :redirect-to="redirectTo">
		<Title>
			{{ webhook.name }}
		</Title>
		<m-input v-model="webhook.name" :label="$t('name')"/>
		<m-input v-model="webhook.url" type="url" :label="$t('url')"/>
		<m-input v-model="webhook.content" :label="$t('content')"/>
		<m-select v-model="webhook.event" :label="$t('webhook_event')" :options="events" :text-by="event => $t('webhook_event_' + event)"/>
	</simple-resource-form>
</template>

<script setup lang="ts">
import type { Game, Webhook } from '~/types/models';

const props = defineProps<{
	game: Game;
}>();

useNeedsPermission('manage-webhooks', props.game);

const redirectTo = computed(() => getAdminUrl('webhooks', props.game));

const events = [
	'mod_approval',
	'mod_approval_new',
	'mod_published',
	'mod_suspended',
	'mod_deleted',
	'report_new'
];

const { data: webhook } = await useEditResource<Webhook>('webhook', 'webhooks', {
	id: 0,
	url: '',
	name: '',
	event: '',
	content: ''
});
</script>
