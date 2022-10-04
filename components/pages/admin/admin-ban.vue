<template>
    <flex class="list-button">
        <flex v-if="ban.case" column>
            <flex class="items-center">User: <a-user :user="ban.user" avatar-size="xs"/></flex>
            <flex class="items-center">
                Issued: <time-ago null-is-never :time="ban.created_at"/>
                <template v-if="ban.case.mod_user">
                    by <a-user :user="ban.case.mod_user" avatar-size="xs"/>
                </template>
            </flex>
            <div>Reason: "{{ban.case.reason}}"</div>
            <div>Duration: {{duration}}</div>
            <div v-if="!isExpired">Expires: <time-ago null-is-never :time="ban.case.expire_date"/></div>
        </flex>
        <span v-else>
            Invalid ban!
        </span>
        <flex class="ml-auto my-auto">
            <a-button :to="`/admin/${casesUrl}/${ban.case_id}`">{{$t('edit')}}</a-button>
            <a-button @click="unban">{{$t('unban')}}</a-button>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import humanizeDuration from 'humanize-duration';
import { DateTime, Interval } from 'luxon';
import { Ban } from '~~/types/models';

const props = defineProps<{
    ban: Ban,
    casesUrl: string
}>();

const now = DateTime.now();

const emit = defineEmits<{
    (e: 'delete', userCase: Ban): void
}>();

const isExpired = computed(() => {
    return props.ban.case.pardoned || now >= DateTime.fromISO(props.ban.case.expire_date);
});

const duration = computed(() => {
    return humanizeDuration(Interval.fromDateTimes(DateTime.fromISO(props.ban.case.created_at), DateTime.fromISO(props.ban.case.expire_date)).toDuration());
});

async function unban() {
    await useDelete(`bans/${props.ban.id}`);
    emit('delete', props.ban);
}
</script>