<template>
    <flex column gap="3">
        <a-form @submit="warn">
            <h2>{{$t('warn_user')}}</h2>
            <flex column>
                <a-user-select v-model="warnUser" required :label="$t('user')"/>
                <a-duration v-model="warnDuration" required :label="$t('duration')"/>
                <a-input v-model="reason" required type="textarea" :label="$t('reason')"/>
                <a-button type="submit" class="mr-auto">{{$t('warn')}}</a-button>
            </flex>
        </a-form>

        <h2>{{$t('cases')}}</h2>
        <a-user-select v-model="user" required :label="$t('user')" clearable/>
        <a-items v-model:page="page" :items="cases" :loading="loading">
            <template #item="{ item }">
                <admin-case :user-case="item" :cases-url="caseItemsUrl" @delete="deleteCase"/>
            </template>
        </a-items>
    </flex>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { Game, UserCase } from '~~/types/models';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const url = computed(() => getGameResourceUrl('user-cases', props.game));
const caseItemsUrl = computed(() => getGameResourceUrl('cases', props.game));

const warnUser = useRouteQuery('user', null, 'number');
const user = useRouteQuery('filter-user', warnUser.value, 'number');
const page = useRouteQuery('page', 1, 'number');

const showErrorToast = useQuickErrorToast();

const { data: cases, loading } = await useWatchedFetchMany<UserCase>(url.value, { page, user_id: user, limit: 5 });

const reason = ref('');
const warnDuration = ref();

async function warn() {
    try {
        const userCase = await postRequest<UserCase>(props.game ? `games/${props.game.id}/user-cases` : 'user-cases', { 
            user_id: warnUser.value,
            reason: reason.value,
            expire_date: warnDuration.value,
        });
        reason.value = '';
        warnUser.value = null;
        cases.value?.data.unshift(userCase);
    } catch (e) {
        showErrorToast(e);
    }
}

function deleteCase(userCase: UserCase) {
    if (cases.value) {
        remove(cases.value.data, userCase);
    }
}
</script>