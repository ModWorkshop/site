<template>
    <m-flex class="list-button">
        <m-flex column :style="{ opacity: isExpired && 0.25 }">
            <m-flex class="items-center">{{$t('user')}}: <a-user :user="ban.user" avatar-size="xs"/></m-flex>
            <m-flex class="items-center">
                {{$t('issued')}}:
                <i18n-t v-if="ban.mod_user" keypath="by_user_time_ago" scope="global">
                    <template #time>
                        <m-time-ago null-is-never :time="ban.created_at"/>
                    </template>
                    <template #user>
                        <a-user :user="ban.mod_user" avatar-size="xs"/>
                    </template>
                </i18n-t>
                <m-time-ago v-else null-is-never :time="ban.created_at"/>
            </m-flex>
            <div>{{$t('reason')}}: "{{ban.reason}}"</div>
            <div>{{$t('duration')}}: {{duration}}</div>
            <div v-if="!isExpired">{{$t('expires')}}: <m-time-ago null-is-never :time="ban.expire_date"/></div>
        </m-flex>
        <m-flex class="ml-auto my-auto">
            <m-button :to="`/admin/${bansUrl}/${ban.id}`">{{$t('edit')}}</m-button>
            <m-button @click="unban">{{$t(isExpired ? 'delete' : 'unban')}}</m-button>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon';
import type { Ban, Game } from '~~/types/models';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    ban: Ban,
    game?: Game,
}>();

const now = DateTime.now();

const { t } = useI18n();

const emit = defineEmits<{
    (e: 'delete', userCase: Ban): void
}>();

const isExpired = computed(() => !props.ban.active || now >= DateTime.fromISO(props.ban.expire_date));
const duration = computed(() => getDuration(t, props.ban.created_at, props.ban.expire_date));
const bansUrl = computed(() => getGameResourceUrl('bans', props.game));

async function unban() {
    await deleteRequest(`bans/${props.ban.id}`);
    emit('delete', props.ban);
}
</script>