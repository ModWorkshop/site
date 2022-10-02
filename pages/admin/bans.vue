<template>
    <flex column gap="3">
        <h2>Ban User</h2>
        <a-user-select v-model="user" label="User"/>
        <a-duration v-model="banDuration" label="Duration"/>
        <a-input v-model="reason" type="textarea" label="Reason"/>
        <a-button class="mr-auto" @click="ban">Ban</a-button>

        <h2>Bans</h2>

        <a-list v-model="bans" url="bans" query>
            <template #item="{ item }">
                <flex class="list-button">
                    <flex v-if="item.case" column>
                        <flex class="items-center">User: <a-user :user="item.user" avatar-size="xs"/></flex>
                        <div>Reason: "{{item.case.reason}}"</div>
                        <div>Expires: <time-ago inline null-is-never :time="item.case.expire_date"/></div>
                    </flex>
                    <span v-else>
                        Invalid ban!
                    </span>
                    <flex class="ml-auto my-auto">
                        <a-button :to="`/admin/cases/${item.case_id}`">{{$t('edit')}}</a-button>
                        <a-button @click="unban(item)">{{$t('unban')}}</a-button>
                    </flex>
                </flex>
            </template>
        </a-list>
    </flex>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { Ban, Game } from '~~/types/models';
import { Paginator } from '~~/types/paginator';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const { showToast } = useToaster();

const bans = ref<Paginator<Ban>>({ data: [], meta: null });

const user = useRouteQuery('user', null, 'number');

const banDuration = ref(null);
const reason = ref('');

async function ban() {
    try {
        const ban = await usePost<Ban>('bans', { 
            user_id: user.value,
            reason: reason.value,
            expire_date: banDuration.value,
        });
        bans.value.data.push(ban);
    } catch (error) {
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