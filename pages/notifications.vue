<template>
    <page-block size="md">
        <content-block>
            <a-list url="notifications" limit="20" :search="false" class="p-4" query>
                <template #buttons="{ items }">
                    <flex class="ml-auto">
                        <a-button color="danger" icon="trash" @click="deleteAll(items.data)">{{$t('delete_all_notifications')}}</a-button>
                        <a-button color="danger" icon="clock" @click="deleteReadNotifications(items.data)">{{$t('delete_read_notifications')}}</a-button>
                        <a-button icon="clock" @click="markAllAsRead(items.data)">{{$t('read_all_notifications')}}</a-button>
                    </flex>
                </template>
                <template #item="{ item, items }">
                    <a-notification :notification="item" :notifications="items"/>
                </template>
            </a-list>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { Notification } from '~~/types/models';

const yesNoModal = useYesNoModal();

async function deleteAll(items: Notification[]) {
    yesNoModal({
        title: 'Are you sure?',
        desc: 'This action cannot be undone!',
        descType: 'danger',
        async yes() {
            await useDelete('notifications');
            items.splice(0);
        }
    });
}

async function deleteReadNotifications(items: Notification[]) {
    yesNoModal({
        title: 'Are you sure?',
        desc: 'This action cannot be undone!',
        descType: 'danger',
        async yes() {
            await useDelete('notifications/read');
            items.forEach((item, i) => {
                if (item.seen) {
                    items.splice(i, 1);
                }
            });
        }
    });
}

async function markAllAsRead(items: Notification[]) {
    yesNoModal({
        title: 'Are you sure?',
        desc: 'This action cannot be undone!',
        descType: 'warning',
        async yes() {
            await usePost('notifications/read-all');
            items.forEach(item => item.seen = true);
        }
    });
}

</script>