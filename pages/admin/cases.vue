<template>
    <flex column gap="3">
        <a-form @submit="warn">
            <h2>{{$t('warn_user')}}</h2>
            <flex column>
                <a-user-select v-model="user" required :label="$t('user')"/>
                <a-duration v-model="warnDuration" required :label="$t('duration')"/>
                <a-input v-model="reason" required type="textarea" :label="$t('reason')"/>
                <a-button type="submit" class="mr-auto">{{$t('warn')}}</a-button>
            </flex>
        </a-form>

        <h2>{{$t('cases')}}</h2>

        <a-list :url="url" query @fetched="(items: Paginator<UserCase>) => cases = items">
            <template #item="{ item }">
                <admin-case :user-case="item" :cases-url="caseItemsUrl" @delete="deleteCase"/>
            </template>
        </a-list>
    </flex>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { Game, UserCase } from '~~/types/models';
import { Paginator } from '~~/types/paginator';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const url = computed(() => getGameResourceUrl('user-cases', props.game));
const caseItemsUrl = computed(() => getGameResourceUrl('cases', props.game));

const user = useRouteQuery('user', null, 'number');

const showErrorToast = useQuickErrorToast();

const cases = ref<Paginator<UserCase>>(null);
const reason = ref('');
const warnDuration = ref();

async function warn() {
    try {
        const userCase = await usePost<UserCase>('user-cases', { 
            user_id: user.value,
            reason: reason.value,
            expire_date: warnDuration.value,
        });
        reason.value = '';
        cases.value.data.push(userCase);
    } catch (e) {
        showErrorToast(e);
    }
}

function deleteCase(userCase: UserCase) {
    remove(cases.value.data, userCase);
}
</script>