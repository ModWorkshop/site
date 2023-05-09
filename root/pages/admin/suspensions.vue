<template>
    <a-items :items="data" :loading="loading">
        <template #buttons>
            <a-input v-model="query" :label="$t('search')"/>
            <a-user-select v-model="userId" :label="$t('user')" clearable/>
        </template>
        <template #items>
            <a-table alt-background>
                <template #head>
                    <th>{{$t('thumbnail')}}</th>
                    <th>{{$t('name')}}</th>
                    <th>{{$t('owner')}}</th>
                    <th>{{$t('date')}}</th>
                    <th>{{$t('active')}}</th>
                    <th style="width: 300px;">{{$t('reason')}}</th>
                    <th>{{$t('actions')}}</th>
                </template>
                <template #body>
                    <mod-row v-for="suspension in data?.data" :key="suspension.id" :mod="suspension.mod" lite :class="{'alt-content-bg': suspension.status}">
                        <template #definitions>
                            <td><time-ago :time="suspension.created_at"/></td>
                            <td>{{suspension.status ? '✔' : '❌'}}</td>
                            <td>{{suspension.reason}}</td>
                            <td>
                                <mod-suspend v-if="suspension.status" :suspension="suspension" :mod="suspension.mod"/>
                                <a-button v-else icon="mdi:delete" @click="deleteSuspension(suspension)">{{ $t('delete') }}</a-button>
                            </td>
                        </template>
                    </mod-row>
                </template>
            </a-table>
        </template>
    </a-items>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { useI18n } from 'vue-i18n';
import { Game, Suspension } from '~~/types/models';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

const userId = useRouteQuery('user', null, 'number');
const query = useRouteQuery('query');
const page = useRouteQuery('page');
const yesNoModal = useYesNoModal();
const { t } = useI18n();

const { data, loading } = await useWatchedFetchMany<Suspension>(getGameResourceUrl('suspensions', props.game), {
    user_id: userId,
    query,
    page
});

useNeedsPermission('manage-mods', props.game);

function deleteSuspension(suspension: Suspension) {
    yesNoModal({
        title: t('are_you_sure'),
        desc: t('irreversible_action'),
        async yes() {
            await useDelete(`suspensions/${suspension.id}`);
            remove(data.value!.data, suspension);
        }
    });
}
</script>