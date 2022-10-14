<template>
    <a-list :url="url" query :params="{ user_id: userId }">
        <template #buttons>
            <a-user-select v-model="userId" clearable/>
        </template>
        <template #items="{ items }">
            <a-table>
                <thead>
                    <th>Thumbnail</th>
                    <th>Name</th>
                    <th>Owner</th>
                    <th>Date</th>
                    <th>Active</th>
                    <th style="width: 300px;">Reason</th>
                    <th>Actions</th>
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

const userId = useRouteQuery('user');
</script>