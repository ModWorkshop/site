<template>
    <flex column gap="3">
        <h2>Ban User</h2>
        <a-user-select v-model="user" label="User"/>
        <a-select v-model="banDuration" :options="banDurations" label="Duration"/>
        <a-input v-model="reason" type="textarea" label="Reason"/>
        <a-button class="mr-auto" @click="ban">Ban</a-button>

        <h2>Bans</h2>
        <flex v-if="bans" column>
            <a-user v-for="banCase of bans.data" :key="banCase.id" :user="banCase.user">
                <template #details>
                    <span>
                        Expires <time-ago inline :time="banCase.expire_date"/>
                    </span>
                </template>
            </a-user>
        </flex>
        <div v-else>
            No cases!
        </div>
    </flex>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon';

const { data: bans } = await useFetchMany('bans');

const user = useRouteQuery<number>('user', null, (data: string) => parseInt(data));

const now = DateTime.now();

const banDurations = [
    { name: 'Day', id: now.plus({ day: 1 }).toISO() },
    { name: 'Week', id: now.plus({ week: 1 }).toISO() },
    { name: 'Permanent', id: '-' },
];

const banDuration = ref(DateTime.now().plus({ day: 1 }));
const reason = ref('');

async function ban() {
    await usePost('bans', { user_id: user.value, reason: reason.value, expire_date: banDuration.value });
}
</script>