<template>
    <m-flex class="list-button">
        <m-flex column :style="{ opacity: isExpired && 0.25 }">
            <m-flex class="items-center">{{$t('user')}}: <a-user :user="userCase.user" avatar-size="xs"/></m-flex>
            <div>{{$t('reason')}}: "{{userCase.reason}}"</div>
            <m-flex class="items-center">
                {{$t('issued')}}:
                <i18n-t v-if="userCase.mod_user" keypath="by_user_time_ago" scope="global">
                    <template #time>
                        <m-time-ago null-is-never :time="userCase.created_at"/>
                    </template>
                    <template #user>
                        <a-user :user="userCase.mod_user" avatar-size="xs"/>
                    </template>
                </i18n-t>
                <m-time-ago v-else null-is-never :time="userCase.created_at"/>
            </m-flex>
            <div v-if="userCase.expire_date">{{$t('duration')}}: {{duration}}</div>
            <div v-if="!isExpired">{{$t('expires')}}: <m-time-ago null-is-never :time="userCase.expire_date"/></div>
            <div v-else>{{$t('expired')}}</div>
        </m-flex>
        <m-flex class="ml-auto my-auto">
            <m-button :to="`/admin/${casesUrl}/${userCase.id}`">{{$t('edit')}}</m-button>
            <m-button color="danger" @click="deleteCase">{{$t('delete')}}</m-button>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import type { UserCase } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { parseISO } from 'date-fns';

const props = defineProps<{
    userCase: UserCase,
    casesUrl: string
}>();

const emit = defineEmits<{
    (e: 'delete', userCase: UserCase): void
}>();

const now = new Date();
const { t } = useI18n();

const yesNoModal = useYesNoModal();

const isExpired = computed(() => !props.userCase.active || now >= parseISO(props.userCase.expire_date));
const duration = computed(() => getDuration(t, props.userCase.created_at, props.userCase.expire_date));

async function deleteCase() {
    yesNoModal({
        desc: t('delete_case_warn'),
        async yes() {
            await deleteRequest(`user-cases/${props.userCase.id}`);
            emit('delete', props.userCase);   
        }
    });
}
</script>