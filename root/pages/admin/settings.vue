<template>
    <a-form v-if="settings" v-model="settings" created float-save-gui :flush-changes="flushChanges" @submit="submit">
        <flex column gap="3">
            <a-input v-model="settings.max_file_size" type="number" label="Max file size"/>
            <a-input v-model="settings.image_max_file_size" type="number" label="Image max file size"/>
            <a-input v-model="settings.mod_storage_size" type="number" label="Storage size per mod"/>
            <a-input v-model="settings.mod_max_image_count" type="number" label="Max images per mod"/>
            <a-input v-model="settings.discord_webhook" label="Discord Webhook for mods"/>
            <a-input v-model="settings.discord_suspension_webhook" label="Discord Webhook for suspensions"/>
            <a-input v-model="settings.discord_approval_webhook" label="Discord Webhook for approvals"/>
            
            <a-select 
                v-model="settings.news_forum_category"
                label="News Forum Category"
                url="forum-categories"
                :fetch-params="{ forum_id: 1 }"
            />
            <a-select 
                v-model="settings.game_requests_forum_category"
                label="Game Request Forum Category"
                url="forum-categories"
                :fetch-params="{ forum_id: 1 }"
            />
        </flex>
    </a-form>
</template>

<script setup lang="ts">
import type { Settings } from '~~/types/models';
useNeedsPermission('admin');

const showError = useQuickErrorToast();
const flushChanges = createEventHook();

const { data: settings } = await useFetchData<Settings>('settings');

async function submit() {
    try {
        await patchRequest('settings', settings.value!);
        flushChanges.trigger(settings);
    } catch (error) {
        showError(error);
    }
}
</script>