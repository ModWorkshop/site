<template>
    <a-form :model="settings" created float-save-gui :ignore-changes="ignoreChanges" @submit="submit">
        <flex column gap="3">
            <a-input v-model="settings.max_file_size" type="number" label="Max file size"/>
            <a-input v-model="settings.image_max_file_size" type="number" label="Image max file size"/>
            <a-input v-model="settings.mod_storage_size" type="number" label="Storage size per mod"/>
            <a-input v-model="settings.mod_max_image_count" type="number" label="Max images per mod"/>
            <a-input v-model="settings.discord_webhook" label="Discord Webhook for mods"/>
        </flex>
    </a-form>
</template>

<script setup lang="ts">
import { Settings } from '~~/types/models';

const { init: showToast } = useToast();

const ignoreChanges = useRaiseEvent();

definePageMeta({
    middleware: 'admins-only',
});

const { data: settings } = await useFetchData<Settings>('settings');

async function submit() {
    try {
        await usePatch('settings', settings.value);
        ignoreChanges.execute();
    } catch (error) {
        console.error(error);
        showToast({ message: error.data.message, color: 'danger' });
        return;
    }
}
</script>