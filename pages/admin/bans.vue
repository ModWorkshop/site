<template>
    <flex column gap="3">
        <h2>{{$t('ban_user')}}</h2>
        <a-user-select v-model="user" :label="$t('user')"/>
        <a-duration v-model="banDuration" :label="$t('duration')"/>
        <a-input v-model="reason" type="textarea" :label="$t('reason')"/>
        <a-button class="mr-auto" @click="ban">{{$t('ban')}}</a-button>

        <h2>{{$t('bans')}}</h2>
        <a-list :url="url" query @fetched="(items: Paginator<Ban>) => bans = items">
            <template #item="{ item }">
                <admin-ban :ban="item" :cases-url="casesUrl" @delete="unban"/>
            </template>
        </a-list>
    </flex>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { useI18n } from 'vue-i18n';
import { Ban, Game } from '~~/types/models';
import { Paginator } from '~~/types/paginator';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const showToast = useQuickErrorToast();
const { t } = useI18n();

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
    } catch (e) {
        showToast(e, {
            405: t('ban_error_405')
        });
    }
}

async function unban(ban: Ban) {
    remove(bans.value.data, ban);
}
</script>