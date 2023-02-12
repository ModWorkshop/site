<template>
    <flex class="list-button">
        <flex column :style="{ opacity: isExpired && 0.25 }">
            <flex class="items-center">{{$t('user')}}: <a-user :user="ban.user" avatar-size="xs"/></flex>
            <flex class="items-center">
                {{$t('issued')}}:
                <i18n-t v-if="ban.mod_user" keypath="by_user_time_ago">
                    <template #time>
                        <time-ago null-is-never :time="ban.created_at"/>
                    </template>
                    <template #user>
                        <a-user :user="ban.mod_user" avatar-size="xs"/>
                    </template>
                </i18n-t>
                <time-ago v-else null-is-never :time="ban.created_at"/>
            </flex>
            <div>{{$t('reason')}}: "{{ban.reason}}"</div>
            <div>{{$t('duration')}}: {{duration}}</div>
            <div v-if="!isExpired">{{$t('expires')}}: <time-ago null-is-never :time="ban.expire_date"/></div>
        </flex>
        <flex class="ml-auto my-auto">
            <a-button :to="`/admin/${bansUrl}/${ban.id}`">{{$t('edit')}}</a-button>
            <a-button @click="unban">{{$t(isExpired ? 'delete' : 'unban')}}</a-button>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon';
import { Ban, Game } from '~~/types/models';

const props = defineProps<{
    ban: Ban,
    game?: Game,
}>();

const now = DateTime.now();

const emit = defineEmits<{
    (e: 'delete', userCase: Ban): void
}>();

const isExpired = computed(() => !props.ban.active || now >= DateTime.fromISO(props.ban.expire_date))
const duration = computed(() => getDuration(props.ban.created_at, props.ban.expire_date));
const bansUrl = computed(() => getGameResourceUrl('bans', props.game));

async function unban() {
    await useDelete(`bans/${props.ban.id}`);
    emit('delete', props.ban);
}
</script>