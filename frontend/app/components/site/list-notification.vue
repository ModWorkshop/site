<template>
	<div :class="classes" @click.prevent.self="onClick">
		<m-avatar v-if="fromUser && !defintion.thumbnail" :src="fromUser?.avatar" :use-thumb="fromUser?.avatar_has_thumb"/>
		<template v-else-if="defintion.thumbnail && defintion.thumbnail.type == 'mod'">
			<mod-thumbnail style="width: 84px;" :thumbnail="defintion.thumbnail.src"/>
		</template>
		<m-flex class="my-auto overflow-hidden" grow wrap column @click.prevent.self="onClick">
			<component
				:is="defintion.component"
				v-if="defintion.component && notifiable"
				class="w-full"
				:notification="notification"
				:notifiable="notifiable"
				:data="data"
			/>
			<i18n-t v-else :keypath="`notification_${notification.type}`" tag="span" style="word-wrap: anywhere;" class="self-start" scope="global">
				<template #user>
					<base-notification type="user" :object="fromUser"/>
				</template>
				<template #context>
					<base-notification :type="notification.context_type" :object="context"/>
				</template>
				<template #notifiable>
					<base-notification :type="notification.notifiable_type" :object="notifiable"/>
				</template>
				<template #extra>
					<base-notification v-if="defintion.extra" :type="defintion.extra.type" :object="defintion.extra.object"/>
					<span v-else>{{ 'not_available' }}</span>
				</template>
			</i18n-t>
			<m-time :datetime="notification.created_at" relative class="self-start"/>
		</m-flex>
		<m-flex class="ml-auto my-auto">
			<m-button v-if="!notification.seen" :title="$t('mark_as_read')" @click="markAsSeen">
				<i-mdi-eye/>
			</m-button>
			<m-button color="danger" :title="$t('delete')" @click="deleteNotification()">
				<i-mdi-delete/>
			</m-button>
		</m-flex>
	</div>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import { storeToRefs } from 'pinia';
import { useStore } from '~/store';
import { type Notification } from '~/types/models';
import { Paginator } from '~/types/paginator.js';

const props = defineProps<{
	notification: Notification;
	notifications: Paginator<Notification>;
	ok?: () => void;
}>();

const notif = toRef(props, 'notification');
const store = useStore();
const router = useRouter();
const { notificationCount } = storeToRefs(store);
const notifiable = computed(() => notif.value.notifiable);
const context = computed(() => notif.value.context);
const fromUser = computed(() => notif.value.from_user);
const data = computed(() => notif.value.data || {});

const defintion = computed(() => (typeDefintions[notif.value.type] || typeDefintions.default)());
const to = computed(() => {
	if (defintion.value.to) {
		return defintion.value.to;
	} else {
		let obj, objType;

		if (defintion.value.link_object == 'context') {
			obj = context.value;
			objType = notif.value.context_type;
		} else {
			obj = notifiable.value;
			objType = notif.value.notifiable_type;
		}

		return !defintion.value.onClick ? getObjectLink(objType, obj) : null;
	}
});

const classes = computed(() => ({
	'notification': true,
	'flex': true,
	'alt-content-bg': true,
	'focus': !notif.value.seen,
	'p-4': true,
	'gap-2': true,
	'cursor-pointer': true
}));

const typeDefintions = {
	default: () => ({
		user() {
			return null;
		}
	}),
	warning: () => ({
		component: resolveComponent('WarningNotification')
	}),
	follow_mod_new_version: () => ({
		extra: {
			object: data.value.version
		}
	}),
	mod_approved: () => ({

	}),
	mod_rejected: () => ({
		component: resolveComponent('ModRejectNotification')
	}),
	mod_suspended: () => ({
		thumbnail: {
			type: 'mod',
			src: notifiable.value.thumbnail
		}
	}),
	mod_unsuspended: () => ({
		thumbnail: {
			type: 'mod',
			src: notifiable.value.thumbnail
		}
	}),
	sub_mod: () => ({
		link_object: 'context'
	}),
	sub_thread: () => ({
		link_object: 'context'
	}),
	sub_comment: () => ({
		link_object: 'context',
		extra: {
			type: notifiable.value.commentable_type,
			object: notifiable.value.commentable
		}
	}),
	transfer_ownership: () => ({
		mod: notifiable.value,
		thumbnail: 'mod'
	}),
	membership_request: () => ({
		mod: notifiable.value,
		thumbnail: 'mod'
	}),
	comment_mention: () => ({
	})
};

async function markAsSeen() {
	await patchRequest(`/notifications/${notif.value.id}`, { seen: true });

	if (!notif.value.seen && notificationCount.value) {
		notificationCount.value = Math.max(0, notificationCount.value - 1);
	}

	notif.value.seen = true;
}

async function deleteNotification(onlyVisually = false) {
	if (!onlyVisually) {
		await deleteRequest(`/notifications/${notif.value.id}`);
	}

	remove(props.notifications.data, notif.value);
}

async function onClick() {
	await markAsSeen();

	router.push(to.value);

	const click = defintion.value.onClick;

	if (click) {
		click();
	}
}

</script>

<style>
.notification {
	color: var(--text-color);
}

.notification:hover {
	color: var(--text-color) !important;
}
</style>
