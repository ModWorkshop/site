<template>
    <flex :class="{'alt-bg-color': !notification.seen, 'p-4': true, 'cursor-pointer': true}" gap="2" @click.stop="onClick">
        <a-avatar v-if="thumbnail == 'user'" :src="showUser?.avatar"/>
        <mod-thumbnail v-if="thumbnail == 'mod'" style="width: 84px;" :mod="mod"/>
        <flex class="my-auto" grow>
            <div>
                <i18n-t :keypath="`notification_${notification.type}`" tag="span" class="whitespace-pre">
                    <template #user>
                        <a-user v-if="showUser" :avatar="false" :user="showUser" @click.stop="ok"/>
                    </template>
                    <template #mod>
                        <a-user v-if="mod" :avatar="false" :user="mod" @click.stop="ok"/>
                    </template>
                    <template #context>
                        <nuxt-link v-if="contextLink" :href="contextLink" @click.stop="ok">{{contextName}}</nuxt-link>
                        <span v-else>{{contextName}}</span>
                    </template>
                    <template #notifiable>
                        <nuxt-link :href="notifiableLink" @click.stop="ok">{{notifiableName}}</nuxt-link>
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
    </flex>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Comment, Notification } from '~~/types/models';
import { Paginator } from '~~/types/paginator.js';
const { init: openModal } = useModal();

const props = defineProps<{
    notification: Notification,
    notifications: Paginator<Notification>,
    ok?: () => void
}>();

const router = useRouter();
const { user, notificationCount } = storeToRefs(useStore());
const notifiable = computed(() => props.notification.notifiable);
const context = computed(() => props.notification.context);

const notifiableName = computed(() => notifiable.value.name || 'ERR');
const contextName = computed(() => context.value?.name || 'ERR');
const notifiableLink = computed(() => objectLink(props.notification.notifiable_type, notifiable.value));
const contextLink = computed(() => objectLink(props.notification.context_type, context.value));

const { t } = useI18n();

function objectLink(type: string, o: Record<string, unknown>) {
    if (!o) {
        return null;
    }
    switch(type) {
        case 'mod':
            return `/mod/${o.id}`;
        case 'user':
            return `/@${o.unique_name}`;
    }

    return null;
}

const typeDefintion = computed(() => typeDefintions[props.notification.type] || typeDefintions.default);
const showUser = computed(() => typeDefintion.value?.user?.());
const mod = computed(() => typeDefintion.value?.mod?.());
const thumbnail = computed(() => typeDefintion.value?.thumbnail || 'user');

async function clickComment(comment: Comment) {
    //TODO: threads
    const page = await useGet(`/mods/${comment.commentable_id}/comments/${comment.id}/page`);
    router.push(`/mod/${comment.commentable_id}?page=${page}`);
    if (props.ok) {
        props.ok();
    }
}

const typeDefintions = {
    default: {
        user() {
            return null;
        }
    },
    mod_comment: {
        onClick() {
            clickComment(context.value);
        },
        user() {
            return context.value?.user;
        }
    },
    transfer_ownership: {
        onClick() {
            function answer(accept: boolean) {
                usePatch(`mods/${notifiable.value.id}/transfer-request/accept`, { accept });
                deleteNotification(true);
            }
            openModal({
                message: t('transfer_request'),
                onOk: () => answer(true),
                onCancel: () => answer(false),
            });
        },
        mod() {
            return notifiable.value;
        },
        thumbnail: 'mod'
    },
    membership_request: {
        onClick() {
            function answer(accept: boolean) {
                usePatch(`mods/${notifiable.value.id}/members/${user.value.id}/accept`, { accept });
                deleteNotification(true);
            }
            openModal({
                message: t('mod_request'),
                onOk: () => answer(true),
                onCancel: () => answer(false),
            });
        },
        mod() {
            return notifiable.value;
        },
        thumbnail: 'mod'
    },
    comment_mention: {
        user() {
            return notifiable.value?.user;
        },
        async onClick() {
            await clickComment(notifiable.value);
        },
    },
    comment_reply: {
        onClick() {
            clickComment(notifiable.value);
        },
        mod() {
            return notifiable.value?.commentable;
        },
        user() {
            return context.value?.user;
        },
    }
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

    const click = typeDefintions[props.notification.type].onClick;
    
    if (click) {
        click();
    } else {
        router.push(notifiableLink.value);
    }
}

</script>