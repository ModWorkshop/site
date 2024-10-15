<template>
    <m-flex column gap="3" style="flex: 1;">
        <m-form reset-on-submit @submit="ban">
            <h2>{{$t('ban_user')}}</h2>
            <m-flex column>
                <user-select v-model="banUser" required :label="$t('user')"/>
                <m-duration v-model="banDuration" :label="$t('duration')"/>
                <m-input v-model="reason" type="textarea" required :label="$t('reason')"/>
                <m-button class="mr-auto" type="submit">{{$t('ban')}}</m-button>
            </m-flex>
        </m-form>

        <h2>{{$t('bans')}}</h2>
        <user-select v-model="user" required :label="$t('user')" clearable/>
        <m-list v-model:page="page" query :items="bans" :loading="loading">
            <template #item="{ item }">
                <admin-ban :ban="item" :game="game" @delete="unban"/>
            </template>
        </m-list>
    </m-flex>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import { useI18n } from 'vue-i18n';
import type { Ban, Game } from '~~/types/models';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('moderate-users', props.game);

const showErrorToast = useQuickErrorToast();
const { showToast } = useToaster();
const { t } = useI18n();

const url = computed(() => getGameResourceUrl('bans', props.game));

const banUser = useRouteQuery('user', null, 'number');
const user = useRouteQuery('filter-user', null, 'number');
const page = ref(1);

const banDuration = ref(null);
const reason = ref('');

const { data: bans, loading } = await useWatchedFetchMany<Ban>(url.value, { page, user_id: user, limit: 20 });

async function ban() {
    try {
        const ban = await postRequest<Ban>(url.value, { 
            user_id: banUser.value,
            reason: reason.value,
            expire_date: banDuration.value,
        });
        if (bans.value) {
            for (const b of bans.value.data) {
                if (b.user_id == banUser.value) {
                    b.active = false;
                }
            }
            bans.value.data.unshift(ban);
        }
        showToast({
            desc: t('successfully_banned'),
            color: 'success'
        });
    } catch (e) {
        showErrorToast(e, {
            405: t('ban_error_405')
        });
    }
}

async function unban(ban: Ban) {
    if (bans.value) {
        remove(bans.value.data, ban);
    }
}
</script>