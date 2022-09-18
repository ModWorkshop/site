<template>
    <flex column gap="3">
        <h2>Warn User</h2>
        <a-user-select v-model="user" label="User"/>
        <flex>
            <a-select v-model="warnDuration" :options="durations" label="Warning Duration"/>
            <a-input v-model="warnDurationCount" label="Count"/>
        </flex>
        <a-input v-model="reason" type="textarea" label="Reason"/>
        <a-button class="mr-auto" @click="warn">{{$t('warn')}}</a-button>

        <h2>Cases</h2>

        <a-list url="user-cases" query>
            <template #item="{ item }">
                <flex class="list-button">
                    <flex column>
                        <flex class="items-center">User: <a-user :user="item.user" avatar-size="xs"/></flex>
                        <div>Reason: "{{item.reason}}"</div>
                        <div>Expires: <time-ago null-is-never :time="item.expire_date"/></div>
                        <div v-if="item.ban">
                            Action: Ban (Expires: <time-ago null-is-never :time="item.ban.expire_date"/>)
                        </div>
                    </flex>
                    <flex class="ml-auto my-auto">
                        <a-button>{{$t('edit')}}</a-button>
                        <a-button>{{$t('pardon')}}</a-button>
                    </flex>
                </flex>
            </template>
        </a-list>
    </flex>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon';

const user = useRouteQuery('user', null, (data: string) => parseInt(data));

const now = DateTime.now();

const durations = [
    { name: 'Days', id: 'days' },
    { name: 'Weeks', id: 'weeks' },
    { name: 'Months', id: 'months' },
    { name: 'Years', id: 'years' },
    { name: 'Permanent', id: '-' },
];

const warnDuration = ref('days');
const warnDurationCount = ref(1);
const reason = ref('');

async function warn() {
    await usePost('user-cases', { user_id: user.value, reason: reason.value, expire_date: warnDuration.value,  });
}
</script>