<template>
    <a-list :url="url" query :params="{ user_id: userId }">
        <template #buttons>
            <a-user-select v-model="userId" :label="$t('user')" clearable/>
        </template>
        <template #items="{ items }">
            <a-table>
                <thead>
                    <th>{{$t('thumbnail')}}</th>
                    <th>{{$t('name')}}</th>
                    <th>{{$t('owner')}}</th>
                    <th>{{$t('date')}}</th>
                    <th>{{$t('active')}}</th>
                    <th style="width: 300px;">{{$t('reason')}}</th>
                    <th>{{$t('actions')}}</th>
                </thead>
                <tbody>
                    <mod-row v-for="suspension in items.data" :key="suspension.id" :mod="suspension.mod" lite>
                        <template #definitions>
                            <td><time-ago :time="suspension.created_at"/></td>
                            <td>{{suspension.status ? '✔' : '❌'}}</td>
                            <td>{{suspension.reason}}</td>
                            <td>
                                <mod-suspend :mod="suspension.mod"/>
                            </td>
                        </template>
                    </mod-row>
                </tbody>
            </a-table>
        </template>
    </a-list>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';
import { getGameResourceUrl } from '~~/utils/helpers';

const props = defineProps<{
    game: Game
}>();

const url = computed(() => getGameResourceUrl('suspensions', props.game));

useNeedsPermission('manage-mods', props.game);

const userId = useRouteQuery('user', null, 'number');
</script>