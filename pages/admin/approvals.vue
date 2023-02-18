<template>
    <a-list :url="url" query :params="{ user_id: userId }">
        <template #buttons>
            <a-user-select v-model="userId" clearable/>
        </template>
        <template #items="{ items }">
            <a-table>
                <template #head>
                    <th>{{$t('thumbnail')}}</th>
                    <th>{{$t('name')}}</th>
                    <th>{{$t('owner')}}</th>
                    <th>{{$t('date')}}</th>
                    <th>{{$t('actions')}}</th>
                </template>
                <template #body>
                    <mod-row v-for="mod in items.data" :key="mod.id" :mod="mod" lite>
                        <template #definitions>
                            <td><time-ago :time="mod.updated_at"/></td>
                             <td>
                                <mod-approve :mod="mod"/>
                            </td>
                        </template>
                    </mod-row>
                </template>
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

useNeedsPermission('manage-mods', props.game);

const url = computed(() => getGameResourceUrl('mods/waiting', props.game));

const userId = useRouteQuery('user');
</script>