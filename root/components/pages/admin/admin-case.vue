<template>
    <flex class="list-button">
        <flex column :style="{ opacity: isExpired && 0.25 }">
            <flex class="items-center">{{$t('user')}}: <a-user :user="userCase.user" avatar-size="xs"/></flex>
            <div>{{$t('reason')}}: "{{userCase.reason}}"</div>
            <flex class="items-center">
                {{$t('issued')}}:
                <i18n-t v-if="userCase.mod_user" keypath="by_user_time_ago" scope="global">
                    <template #time>
                        <time-ago null-is-never :time="userCase.created_at"/>
                    </template>
                    <template #user>
                        <a-user :user="userCase.mod_user" avatar-size="xs"/>
                    </template>
                </i18n-t>
                <time-ago v-else null-is-never :time="userCase.created_at"/>
            </flex>
            <div v-if="userCase.expire_date">{{$t('duration')}}: {{duration}}</div>
            <div v-if="!isExpired">{{$t('expires')}}: <time-ago null-is-never :time="userCase.expire_date"/></div>
            <div v-else>{{$t('expired')}}</div>
        </flex>
        <flex class="ml-auto my-auto">
            <a-button :to="`/admin/${casesUrl}/${userCase.id}`">{{$t('edit')}}</a-button>
            <a-button color="danger" @click="deleteCase">{{$t('delete')}}</a-button>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon';
import { UserCase } from '~~/types/models';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    userCase: UserCase,
    casesUrl: string
}>();

const emit = defineEmits<{
    (e: 'delete', userCase: UserCase): void
}>();

const now = DateTime.now();
const { t } = useI18n();

const yesNoModal = useYesNoModal();

const isExpired = computed(() => !props.userCase.active || now >= DateTime.fromISO(props.userCase.expire_date));
const duration = computed(() => getDuration(props.userCase.created_at, props.userCase.expire_date));

async function deleteCase() {
    yesNoModal({
        desc: t('delete_case_warn'),
        async yes() {
            await useDelete(`user-cases/${props.userCase.id}`);
            emit('delete', props.userCase);   
        }
    });
}
</script>