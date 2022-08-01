<template>
    <flex :class="{'alt-bg-color': !notification.seen, 'p-4': true, 'cursor-pointer': true}" gap="2" @click.stop="onClick">
        <a-avatar v-if="user" :src="user.avatar"/>
        <mod-thumbnail v-if="mod" style="width: 84px;" :mod="mod"/>
        <flex class="my-auto" grow>
            <div>
                <i18n-t :keypath="`notification_${notification.type}`" tag="span">
                    <template #user>
                        <a-user v-if="user" :avatar="false" :user="user" @click.stop="ok"/>
                    </template>
                    <template #context>
                        <nuxt-link :href="contextLink" @click.stop="ok">{{context}}</nuxt-link>
                    </template>
                    <template #notifiable>
                        <nuxt-link :href="notifiableLink" @click.stop="ok">{{notifiable}}</nuxt-link>
                    </template>
                </i18n-t>
                <br>
                <time-ago :time="notification.created_at"/>
            </div>
            <flex class="ml-auto">
                <a-button v-if="!notification.seen" icon="check" title="Mark as Seen" @click.stop="markAsSeen"/>
                <a-button icon="trash" color="danger" title="Delete" @click.stop="deleteNotification"/>
            </flex>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useStore } from '~~/store';
import { Notification } from '~~/types/models';

const props = defineProps<{
    notification: Notification,
    ok: () => void
}>();


const router = useRouter();
const { notifications } = storeToRefs(useStore());
const notifiable = computed(() => props.notification.notifiable?.name || 'ERR');

function objectLink(type: string, o: any) {
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
const notifiableLink = computed(() => objectLink(props.notification.notifiable_type, props.notification.notifiable));
const contextLink = computed(() => objectLink(props.notification.context_type, props.notification.context));
const user = computed(() => typeDefintion.value?.user?.());
const mod = computed(() => typeDefintion.value?.mod?.());
const context = computed(() => typeDefintion.value?.context?.());

const typeDefintions = {
    default: {
        user() {
            return null;
        }
    },
    mod_comment: {
        onClick() {
            router.push(`/mod/${props.notification.notifiable_id}`);
            props.ok();
        },
        user() {
            return props.notification.context?.user;
        }
    }
};

async function markAsSeen() {
    await usePatch(`/notifications/${props.notification.id}`, { seen: true });
    props.notification.seen = true;
}

async function deleteNotification() {
    await useDelete(`/notifications/${props.notification.id}`);
    notifications.value.data = notifications.value.data.filter(notif => notif.id !== props.notification.id);
}

async function onClick() {
    props.notification.seen = true;
    if (notifications.value) {
        notifications.value.total_unseen--;
    }

    const click = typeDefintions[props.notification.type].onClick;
    if (click) {
        click();
    }
}

</script>