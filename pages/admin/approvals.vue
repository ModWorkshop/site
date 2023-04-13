<template>
    <a-items :items="mods">
        <template #buttons>
            <a-input v-model="query" :label="$t('search')"/>
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
                    <mod-row v-for="mod in items!.data" :key="mod.id" :mod="mod" lite>
                        <template #definitions>
                            <td><time-ago :time="mod.updated_at"/></td>
                                <td>
                                <mod-approve :mod="mod" :mods="mods!.data"/>
                            </td>
                        </template>
                    </mod-row>
                </template>
            </a-table>
        </template>
    </a-items>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';
import { getGameResourceUrl } from '~~/utils/helpers';
import { Mod } from '../../types/models';

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-mods', props.game);

const url = computed(() => getGameResourceUrl('mods/waiting', props.game));

const page = useRouteQuery('page', 1);
const query = useRouteQuery('query', '');
const userId = useRouteQuery('user');

const { data: mods } = await useWatchedFetchMany<Mod>(url.value, { page, query, user_id: userId });

</script>