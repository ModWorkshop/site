<template>
    <m-list :items="data" :loading="loading">
        <template #buttons>
            <m-input v-model="query" :label="$t('search')"/>
            <user-select v-model="userId" :label="$t('user')" clearable/>
        </template>
        <template #items>
            <m-table alt-background>
                <template #head>
                    <th>{{$t('thumbnail')}}</th>
                    <th>{{$t('name')}}</th>
                    <th>{{$t('owner')}}</th>
                    <th>{{$t('date')}}</th>
                    <th>{{$t('active')}}</th>
                    <th>{{$t('reason')}}</th>
                    <th>{{$t('actions')}}</th>
                </template>
                <template #body>
                    <mod-row v-for="suspension in data?.data" :key="suspension.id" :mod="suspension.mod" lite :class="{'alt-content-bg': suspension.status}">
                        <template #definitions>
                            <td><m-time :datetime="suspension.created_at" relative/></td>
                            <td>{{suspension.status ? '✔' : '❌'}}</td>
                            <td style="min-width: 300px;" class="whitespace-break-spaces">{{suspension.reason}}</td>
                            <td>
                                <mod-suspend v-if="suspension.status" :suspension="suspension" :mod="suspension.mod"/>
                                <m-button v-else color="danger" @click="deleteSuspension(suspension)"><i-mdi-delete/> {{ $t('delete') }}</m-button>
                            </td>
                        </template>
                    </mod-row>
                </template>
            </m-table>
        </template>
    </m-list>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import { useI18n } from 'vue-i18n';
import type { Game, Suspension } from '~~/types/models';
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
            await deleteRequest(`suspensions/${suspension.id}`);
            remove(data.value!.data, suspension);
        }
    });
}
</script>