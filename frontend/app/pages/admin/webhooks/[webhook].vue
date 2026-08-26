<template>
	<simple-resource-form v-model="webhook" url="webhooks" :game="game" :redirect-to="redirectTo">
		<Title>
			{{ webhook.name }}
		</Title>
		<m-input v-model="webhook.is_active" type="checkbox" :label="$t('active')"/>
		<m-input v-model="webhook.name" :label="$t('name')"/>
		<m-input v-model="webhook.url" type="url" :label="$t('url')"/>
		<m-input v-model="webhook.custom_template" :label="$t('webhook_custom_template')" :desc="$t('webhook_custom_template_help')" type="textarea" rows="12"/>

		<m-input v-for="event of events" :key="event" v-model="webhook[event]" :label="$t('webhook_' + event)" type="checkbox"/>
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
	'event_mod_approval',
	'event_mod_approval_new',
	'event_mod_published',
	'event_mod_suspended',
	'event_mod_deleted',
	'event_report_new',
	'event_mod_updated',
	'event_mod_bumped',
	'event_file_uploaded'
];

const { data: webhook } = await useEditResource<Webhook>('webhook', 'webhooks', {
	id: 0,
	url: '',
	name: '',
	custom_template: '',
	is_active: true,
	event_mod_approval: false,
	event_mod_approval_new: false,
	event_mod_deleted: false,
	event_mod_suspended: false,
	event_mod_published: false,
	event_mod_updated: false,
	event_mod_bumped: false,
	event_file_uploaded: false,
	event_report_new: false
});
</script>
