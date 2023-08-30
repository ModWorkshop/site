<template>
    <page-block size="md">
        <Title>{{$t('notifications')}}</Title>
        <content-block>
            <flex class="ml-auto">
                <a-button color="danger" @click="deleteAll()"><i-mdi-delete/> {{$t('delete_all_notifications')}}</a-button>
                <a-button color="danger" @click="deleteReadNotifications()"><i-mdi-clock/> {{$t('delete_seen_notifications')}}</a-button>
                <a-button @click="markAllAsRead()"><i-mdi-clock/> {{$t('mark_all_notifications')}}</a-button>
            </flex>
            <a-items :items="notifications" :loading="loading">
                <template #item="{ item }">
                    <a-notification :notification="item" :notifications="notifications!"/>
                </template>
            </a-items>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Notification } from '~~/types/models';

definePageMeta({
    middleware: 'users-only',
});

const yesNoModal = useYesNoModal();
const { t } = useI18n();
const page = useRouteQuery('page', 1);

const { data: notifications, loading } = await useWatchedFetchMany<Notification>('notifications', { page, limit: 20 });

async function deleteAll() {
    yesNoModal({
        title: t('are_you_sure'),
        desc: t('irreversible_action'),
        descType: 'danger',
        async yes() {
            await deleteRequest('notifications');
            if (notifications.value) {
                notifications.value.data.splice(0);
            }
        }
    });
}

async function deleteReadNotifications() {
    yesNoModal({
        title: t('are_you_sure'),
        desc: t('irreversible_action'),
        descType: 'danger',
        async yes() {
            await deleteRequest('notifications/read');
            if (notifications.value) {
                notifications.value.data.forEach((item, i) => {
                    if (item.seen) {
                        notifications.value!.data.splice(i, 1);
                    }
                });
            }
        }
    });
}

async function markAllAsRead() {
    await postRequest('notifications/read-all');
    if (notifications.value) {
        notifications.value.data.forEach(item => item.seen = true);
    }
}

</script>