<template>
    <flex column gap="3">
        <h2>Ban User</h2>
        <a-user-select v-model="user" label="User"/>
        <a-select v-model="banDuration" :options="banDurations" label="Duration"/>
        <a-input v-model="reason" type="textarea" label="Reason"/>
        <a-button class="mr-auto" @click="ban">Ban</a-button>

        <h2>Bans</h2>

        <a-list v-model="bans" url="bans" query>
            <template #item="{ item }">
                <flex class="list-button">
                    <flex column>
                        <flex class="items-center">User: <a-user :user="item.user" avatar-size="xs"/></flex>
                        <div>Reason: "{{item.case?.reason}}"</div>
                        <div>Expires: <time-ago inline null-is-never :time="item.expire_date"/></div>
                    </flex>
                    <flex class="ml-auto my-auto">
                        <a-button>{{$t('edit')}}</a-button>
                        <a-button @click="unban(item)">{{$t('unban')}}</a-button>
                    </flex>
                </flex>
            </template>
        </a-list>
    </flex>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { DateTime } from 'luxon';
import { Ban } from '~~/types/models';
import { Paginator } from '~~/types/paginator';

const { showToast } = useToaster();

const bans = ref<Paginator<Ban>>({ data: [], meta: null });

const user = useRouteQuery('user', null, (data: string) => parseInt(data));

const now = DateTime.now();

const banDurations = [
    { name: 'Day', id: now.plus({ day: 1 }).toISO() },
    { name: 'Week', id: now.plus({ week: 1 }).toISO() },
    { name: 'Permanent', id: '' },
];

const banDuration = ref(DateTime.now().plus({ day: 1 }));
const reason = ref('');

async function ban() {
    try {
        const ban = await usePost<Ban>('bans', { user_id: user.value, reason: reason.value, expire_date: banDuration.value });
        bans.value.data.push(ban);
    } catch (error) {
        console.log(error.response);
        
        const errorCodes = {
            405: 'User already banned. Either edit the duration or unban the user.'
        };

        showToast({
            title: 'Failed to ban user',
            desc: errorCodes[error.response.status],
            color: 'danger',
        });
    }
}

async function unban(ban: Ban) {
    await useDelete(`bans/${ban.id}`);
    remove(bans.value.data, ban);
}
</script>