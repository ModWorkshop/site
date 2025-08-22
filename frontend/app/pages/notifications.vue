<template>
	<page-block size="md">
		<Title>{{ $t('notifications') }}</Title>
		<m-content-block>
			<m-flex class="ml-auto">
				<m-button color="danger" @click="deleteAll"><i-mdi-delete/> {{ $t('delete_all_notifications') }}</m-button>
				<m-button color="danger" @click="deleteReadNotifications"><i-mdi-clock/> {{ $t('delete_seen_notifications') }}</m-button>
				<m-button @click="markAsRead"><i-mdi-clock/> {{ $t('mark_all_notifications') }}</m-button>
			</m-flex>
			<m-list v-model:page="page" query :items="notifications" :loading="loading">
				<template #item="{ item }">
					<list-notification :notification="item" :notifications="notifications!"/>
				</template>
			</m-list>
		</m-content-block>
	</page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { Notification } from '~/types/models';
import { useStore } from '../store/index';
import { storeToRefs } from 'pinia';

definePageMeta({
	middleware: 'users-only'
});

const yesNoModal = useYesNoModal();
const { t } = useI18n();
const page = ref(1);
const { notificationCount } = storeToRefs(useStore());

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

async function markAsRead() {
	await markAllNotificationsAsRead(notifications.value?.data, notificationCount);
}
</script>
