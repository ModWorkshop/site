<template>
    <component :is="to ? NuxtLink : 'div'" :to="to" :class="classes" @click.stop="onClick">
        <a-avatar v-if="!defintion.thumbnail" :src="fromUser?.avatar"/>
        <template v-else>
            <mod-thumbnail v-if="defintion.thumbnail.type == 'mod'" style="width: 84px;" :thumbnail="defintion.thumbnail.src"/>
        </template>
        <flex class="my-auto" grow>
            <div>
                <i18n-t :keypath="`notification_${notification.type}`" tag="span" vclass="whitespace-pre">
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
                <br>
                <time-ago :time="notification.created_at"/>
            </div>
            <flex class="ml-auto my-auto">
                <a-button v-if="!notification.seen" icon="check" :title="$t('mark_as_read')" @click.prevent="markAsSeen"/>
                <a-button icon="mdi:trash" color="danger" :title="$t('delete')" @click.prevent="deleteNotification()"/>
            </flex>
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
const { user, notificationCount } = storeToRefs(useStore());
const notifiable = computed(() => notif.value.notifiable);
const context = computed(() => notif.value.context);
const fromUser = computed(() => notif.value.from_user);
const data = computed(() => notif.value.data || {});

const defintion = computed(() => (typeDefintions[notif.value.type] || typeDefintions.default)());
const to = computed(() => !defintion.value.onClick ? getObjectLink(notif.value.notifiable_type, notifiable.value) : null);

const classes = computed(() => ({
    notification: true,
    flex: true,
    'alt-bg-color': !notif.value.seen,
    'p-4': true,
    'gap-2': true,
    'cursor-pointer': true
}));

async function clickComment(comment: Comment) {
    const page = await useGet(`/${comment.commentable_type}s/${comment.commentable_id}/comments/${comment.id}/page`);
    if (comment.reply_to) {
        router.push(`/${comment.commentable_type}/${comment.commentable_id}/post/${comment.reply_to}?page=${page}`);
    } else {
        router.push(`/${comment.commentable_type}/${comment.commentable_id}?page=${page}`);
    }
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
    follow_mod_new_version: () => ({
        extra: {
            object: data.value.version
        }
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
        onClick() {
            async function answer(accept: boolean) {
                await usePatch(`mods/${notifiable.value.id}/transfer-request/accept`, { accept });
                deleteNotification(true);
            }
            yesNoModal({
                desc: t('transfer_request'),
                yes: async () => await answer(true),
                no: async () => await answer(false),
            });
        },
        mod: notifiable.value,
        thumbnail: 'mod'
    }),
    membership_request: () => ({
        onClick() {
            async function answer(accept: boolean) {
                await usePatch(`mods/${notifiable.value.id}/members/${user.value.id}/accept`, { accept });
                deleteNotification(true);
            }
            yesNoModal({
                desc: t('mod_request'),
                yes: async () => await answer(true),
                no: async () => await answer(false),
            });
        },
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
    notif.value.seen = true;
}

async function deleteNotification(onlyVisually=false) {
    if (!onlyVisually) {
        await useDelete(`/notifications/${notif.value.id}`);
    }

    remove(props.notifications.data, notif.value);
}

async function onClick() {
    if (!notif.value.seen) {
        notificationCount.value--;
    }

    if (!notif.value.seen) {
        await usePatch(`notifications/${notif.value.id}`, {
            seen: true
        });
    }

    notif.value.seen = true;

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