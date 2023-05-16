<template>
    <flex column gap="3">
        <h2>{{$t('upgrade_user')}}</h2>
        <a-user-select v-model="user" :label="$t('user')"/>
        <a-duration v-model="duration" :label="$t('duration')"/>
        <a-button class="mr-auto" :disabled="!user" @click="upgrade">{{$t('upgrade')}}</a-button>

        <h2>{{$t('supporters')}}</h2>
        <a-items v-model:page="page" :items="supporters" :loading="loading">
            <template #item="{ item }">
                <flex class="list-button" :style="{ opacity: item.expired ? 0.5 : 1 }">
                    <flex column>
                        <a-user :user="item.user"/>
                        <div>
                            {{ $t('date') }}: <time-ago :time="item.created_at"/>
                        </div>
                        <div v-if="!item.expired">
                            {{ $t('expires') }}: <time-ago :time="item.expire_date"/>
                        </div>
                        <div v-else>
                            {{ $t('expired') }}
                        </div>
                    </flex>
                    <a-button class="ml-auto self-center" icon="mdi:trash" @click="removeSupporter(item)">{{$t('stop')}}</a-button>
                </flex>
            </template>
        </a-items>
    </flex>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { useI18n } from 'vue-i18n';
import { Supporter } from '~~/types/models';

useNeedsPermission('manage-users');

const user = ref(null);
const duration = ref(null);
const page = useRouteQuery('page', 1);

const yesNoModal = useYesNoModal();
const { showToast } = useToaster();
const { t } = useI18n();

const { data: supporters, loading } = await useWatchedFetchMany<Supporter>('supporters', { page });

async function upgrade() {
    try {
        const supporter = await postRequest<Supporter>('supporters', {
            user_id: user.value,
            duration: duration.value
        });
        supporters.value?.data.unshift(supporter);
    } catch (error) {
        showToast({
            color: 'danger',
            desc: t('could_not_upgrade_user')
        });
    }
}

function removeSupporter(supporter: Supporter) {
    yesNoModal({
        title: t('stop_supporter_status'),
        desc: t('stop_supporter_status_desc'),
        async yes() {
            await deleteRequest(`supporters/${supporter.id}`);
            if (supporters.value?.data) {
                remove(supporters.value.data, supporter);
            }
        }
    });
}
</script>