<template>
    <flex class="list-button">
        <flex column :style="{ opacity: isExpired && 0.25 }">
            <div>
                <a-tag>{{userCase.warning ? 'Warning' : 'Ban'}}</a-tag>
            </div>
            <flex class="items-center">User: <a-user :user="userCase.user" avatar-size="xs"/></flex>
            <div>Reason: "{{userCase.reason}}"</div>
            <flex class="items-center">
                Issued: <time-ago null-is-never :time="userCase.created_at"/>
                <template v-if="userCase.mod_user">
                    by <a-user :user="userCase.mod_user" avatar-size="xs"/>
                </template>
            </flex>
            <div>Duration: {{duration}}</div>
            <div v-if="!isExpired">Expires: <time-ago null-is-never :time="userCase.expire_date"/></div>
            <div v-else-if="userCase.pardoned">Pardoned: {{userCase.pardon_reason}}</div>
            <div v-else>Expired</div>
        </flex>
        <flex class="ml-auto my-auto">
            <a-button :to="`/admin/${casesUrl}/${userCase.id}`">{{$t('edit')}}</a-button>
            <a-button color="danger" @click="deleteCase">{{$t('delete')}}</a-button>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { DateTime, Interval } from 'luxon';
import { UserCase } from '~~/types/models';
import humanizeDuration  from 'humanize-duration';

const props = defineProps<{
    userCase: UserCase,
    casesUrl: string
}>();

const emit = defineEmits<{
    (e: 'delete', userCase: UserCase): void
}>();

const now = DateTime.now();

const yesNoModal = useYesNoModal();

const isExpired = computed(() => {
    return props.userCase.pardoned || now >= DateTime.fromISO(props.userCase.expire_date);
});

const duration = computed(() => {
    return humanizeDuration(Interval.fromDateTimes(DateTime.fromISO(props.userCase.created_at), DateTime.fromISO(props.userCase.expire_date)).toDuration());
});

async function deleteCase() {
    yesNoModal({
        desc: 'Are you sure you want to delete the case? If this is a ban, it will unban the user!',
        async yes() {
            await useDelete(`user-cases/${props.userCase.id}`);
            emit('delete', props.userCase);   
        }
    });
}
</script>