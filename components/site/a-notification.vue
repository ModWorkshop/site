<template>
    <component :is="to ? 'NuxtLink' : 'div'" :class="classes" @click.stop="onClick">
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
                        <span v-else>N/A</span>
                    </template>
                </i18n-t>
                <br>
                <time-ago :time="notification.created_at"/>
            </div>
            <flex class="ml-auto my-auto">
                <a-button v-if="!notification.seen" icon="check" title="Mark as Seen" @click.stop="markAsSeen"/>
                <a-button icon="trash" color="danger" title="Delete" @click.stop="deleteNotification()"/>
            </flex>
        </flex>
    </component>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Comment, Notification } from '~~/types/models';
import { Paginator } from '~~/types/paginator.js';
const yesNoModal = useYesNoModal();

const { t } = useI18n();

const props = defineProps<{
    notification: Notification,
    notifications: Paginator<Notification>,
    ok?: () => void
}>();

const router = useRouter();
const { user, notificationCount } = storeToRefs(useStore());
const notifiable = computed(() => props.notification.notifiable);
const context = computed(() => props.notification.context);
const fromUser = computed(() => props.notification.from_user);
const data = computed(() => props.notification.data || {});

const defintion = computed(() => (typeDefintions[props.notification.type] || typeDefintions.default)());
const to = computed(() => !defintion.value.onClick ? getObjectLink(props.notification.notifiable_type, notifiable.value) : null);

const classes = computed(() => ({
    'alt-bg-color': !props.notification.seen,
    'p-4': true,
    'gap-2': true,
    'flex': true,
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
    await usePatch(`/notifications/${props.notification.id}`, { seen: true });
    props.notification.seen = true;
}

async function deleteNotification(onlyVisually=false) {
    if (!onlyVisually) {
        await useDelete(`/notifications/${props.notification.id}`);
    }
    props.notifications.data = props.notifications.data.filter(notif => notif.id !== props.notification.id);
}

async function onClick() {
    if (!props.notification.seen) {
        notificationCount.value--;
    }
    props.notification.seen = true;

    const click = defintion.value.onClick;
    
    if (click) {
        click();
    }
}

</script>