<template>
    <component :is="to ? NuxtLink : 'div'" :to="to" :class="classes" @click.prevent="onClick">
        <a-avatar v-if="fromUser && !defintion.thumbnail" :src="fromUser?.avatar"/>
        <template v-else-if="defintion.thumbnail && defintion.thumbnail.type == 'mod'">
            <mod-thumbnail style="width: 84px;" :thumbnail="defintion.thumbnail.src"/>
        </template>
        <flex class="my-auto overflow-hidden" grow wrap>
            <component 
                :is="defintion.component"
                v-if="defintion.component && notifiable"
                class="w-full"
                :notification="notification"
                :notifiable="notifiable"
                :data="data"
            />
            <i18n-t v-else :keypath="`notification_${notification.type}`" tag="span" class="w-full" style="word-wrap: anywhere;">
                <template #user>
                    <a-notification-slot type="user" :object="fromUser"/>
                </template>
                <template #context>
                    <a-notification-slot :type="notification.context_type" :object="context"/>
                </template>
                <template #notifiable>
                    <a-notification-slot :type="notification.notifiable_type" :object="notifiable"/>
                </template>
                <template #extra>
                    <a-notification-slot v-if="defintion.extra" :type="defintion.extra.type" :object="defintion.extra.object"/>
                    <span v-else>{{'not_available'}}</span>
                </template>
            </i18n-t>
            <time-ago :time="notification.created_at"/>
        </flex>
        <flex class="ml-auto my-auto">
            <a-button v-if="!notification.seen" icon="ic:baseline-remove-red-eye" :title="$t('mark_as_read')" @click.stop="markAsSeen"/>
            <a-button icon="mdi:trash" color="danger" :title="$t('delete')" @click.stop="deleteNotification()"/>
        </flex>
    </component>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { storeToRefs } from 'pinia';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Comment, Notification } from '~~/types/models';
import { Paginator } from '~~/types/paginator.js';
import { getObjectLink } from '~~/utils/helpers';
const yesNoModal = useYesNoModal();

const NuxtLink = resolveComponent('NuxtLink');

const { t } = useI18n();

const props = defineProps<{
    notification: Notification,
    notifications: Paginator<Notification>,
    ok?: () => void
}>();

const router = useRouter();
const notif = toRef(props, 'notification');
const store = useStore();
const { notificationCount } = storeToRefs(store);
const notifiable = computed(() => notif.value.notifiable);
const context = computed(() => notif.value.context);
const fromUser = computed(() => notif.value.from_user);
const data = computed(() => notif.value.data || {});

const defintion = computed(() => (typeDefintions[notif.value.type] || typeDefintions.default)());
const to = computed(() => !defintion.value.onClick ? getObjectLink(notif.value.notifiable_type, notifiable.value) : null);

const classes = computed(() => ({
    notification: true,
    flex: true,
    'alt-content-bg': true,
    'focus': !notif.value.seen,
    'p-4': true,
    'gap-2': true,
    'cursor-pointer': true
}));

async function clickComment(comment: Comment) {
    router.push(await getCommentLink(comment));
    if (props.ok) {
        props.ok();
    }
}

const typeDefintions = {
    default: () => ({
        user() {
            return null;
        }
    }),
    warning: () => ({
        component: resolveComponent('AWarningNotification')
    }),
    follow_mod_new_version: () => ({
        extra: {
            object: data.value.version
        }
    }),
    mod_approved: () => ({
        
    }),
    mod_rejected: () => ({
        component: resolveComponent('AModRejectNotification')
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
        onClick() {
            clickComment(context.value);
        },
    }),
    sub_thread: () => ({
        onClick() {
            clickComment(context.value);
        },
    }),
    sub_comment: () => ({
        onClick() {
            clickComment(context.value);
        },
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
        async onClick() {
            await clickComment(notifiable.value);
        },
    }),
};

async function markAsSeen() {
    await usePatch(`/notifications/${notif.value.id}`, { seen: true });

    if (!notif.value.seen && notificationCount.value) {
        notificationCount.value = Math.max(0, notificationCount.value-1);
    }

    notif.value.seen = true;
}

async function deleteNotification(onlyVisually=false) {
    if (!onlyVisually) {
        await useDelete(`/notifications/${notif.value.id}`);
    }

    remove(props.notifications.data, notif.value);
}

async function onClick() {
    await markAsSeen();

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