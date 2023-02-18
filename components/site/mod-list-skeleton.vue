<template>
    <div v-if="displayMode == 0" class="mods mods-grid gap-2">
        <div v-if="error">{{$t('error_fetching_mods')}}</div>
        <template v-else>
            <a-mod v-for="mod in mods" :key="mod.id" :mod="mod" :game="game" :no-game="noGame" :sort="sortBy"/>
        </template>
    </div>
    <a-table v-else>
        <template #head>
            <th v-if="displayMode == 1">{{$t('thumbnail')}}</th>
            <th>{{$t('name')}}</th>
            <th>{{$t('owner')}}</th>
            <th>{{!!noGame ? $t('category') : $t('game_category')}}</th>
            <th class="text-center">{{$t('likes')}}</th>
            <th class="text-center">{{$t('downloads')}}</th>
            <th class="text-center">{{$t('views')}}</th>
            <th class="text-center">{{sortBy == 'published_at' ? $t('published_at') : $t('last_updated')}}</th>
        </template>
        <template #body>
            <mod-row v-for="mod in mods" :key="mod.id" :mod="mod" :no-game="noGame" :sort="sortBy" :display-mode="displayMode"/>
        </template>
    </a-table>
</template>

<script setup lang="ts">
import { Game, Mod } from '~~/types/models';

defineProps<{
    displayMode: number,
    sortBy: string,
    game: Game,
    noGame: boolean,
    error: true|Error,
    mods: Mod[]
}>();
</script>