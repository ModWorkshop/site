<template>
    <flex column gap="3">
        <h2>Ban User</h2>
        <a-user-select v-model="user" label="User"/>
        <a-duration v-model="banDuration" label="Duration"/>
        <a-input v-model="reason" type="textarea" label="Reason"/>
        <a-button class="mr-auto" @click="ban">Ban</a-button>

        <h2>Bans</h2>

        <a-list :url="url" query @fetched="(items: Paginator<Ban>) => bans = items">
            <template #item="{ item }">
                <admin-ban :ban="item" :cases-url="casesUrl" @delete="unban"/>
            </template>
        </a-list>
    </flex>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { Ban, Game } from '~~/types/models';
import { Paginator } from '~~/types/paginator';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const { showToast } = useToaster();

const url = computed(() => getGameResourceUrl('bans', props.game));
const casesUrl = computed(() => getGameResourceUrl('cases', props.game));
const bans = ref<Paginator<Ban>>(null);

const user = useRouteQuery('user', null, 'number');

const banDuration = ref(null);
const reason = ref('');

async function ban() {
    try {
        const ban = await usePost<Ban>(url.value, { 
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
    remove(bans.value.data, ban);
}
</script>