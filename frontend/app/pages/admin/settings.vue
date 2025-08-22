<template>
	<m-form v-if="settings" v-model="settings" created float-save-gui :flush-changes="flushChanges" @submit="submit">
		<m-flex column gap="3">
			<m-input v-model="settings.max_file_size" type="number" label="Max file size"/>
			<m-input v-model="settings.image_max_file_size" type="number" label="Image max file size"/>
			<m-input v-model="settings.mod_storage_size" type="number" label="Storage size per mod"/>
			<m-input v-model="settings.supporter_mod_storage_size" type="number" label="Supporter Storage size per mod"/>
			<m-input v-model="settings.mod_max_image_count" type="number" label="Max images per mod"/>
			<m-input v-model="settings.discord_webhook" label="Discord Webhook for mods"/>
			<m-input v-model="settings.discord_suspension_webhook" label="Discord Webhook for suspensions"/>
			<m-input v-model="settings.discord_approval_webhook" label="Discord Webhook for approvals"/>
			<m-input v-model="settings.edit_comment_threshold" type="number" label="Comment editing threshold"/>

			<m-select
				v-model="settings.news_forum_category"
				label="News Forum Category"
				url="forum-categories"
				:fetch-params="{ forum_id: 1 }"
			/>
			<m-select
				v-model="settings.game_requests_forum_category"
				label="Game Request Forum Category"
				url="forum-categories"
				:fetch-params="{ forum_id: 1 }"
			/>
		</m-flex>
	</m-form>
</template>

<script setup lang="ts">
import type { Settings } from '~/types/models';
import clone from 'rfdc/default';
useNeedsPermission('admin');

const showError = useQuickErrorToast();
const flushChanges = createEventHook();

const { data } = await useFetchData<Settings>('settings');

const settings = ref(clone(data.value));

async function submit() {
	try {
		flushChanges.trigger(await patchRequest('settings', settings.value!));
	} catch (error) {
		showError(error);
	}
}
</script>
