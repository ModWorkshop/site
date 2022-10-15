<template>
    <div v-if="displayMode == 0" class="mods mods-grid gap-2">
        <div v-if="error">{{$t('error_fetching_mods')}}</div>
        <template v-else>
            <a-mod v-for="mod in mods" :key="mod.id" :mod="mod" :no-game="noGame" :sort="sortBy"/>
        </template>
    </div>
    <table v-else style="border-spacing: 0.5rem 0.25rem;">
        <thead>
            <tr>
                <th v-if="displayMode == 1">{{$t('thumbnail')}}</th>
                <th>{{$t('name')}}</th>
                <th>{{$t('owner')}}</th>
                <th>{{!!noGame ? $t('category') : $t('game_category')}}</th>
                <th>{{$t('likes')}}</th>
                <th>{{$t('downloads')}}</th>
                <th>{{$t('views')}}</th>
                <th>{{sortBy == 'published_at' ? $t('published_at') : $t('last_updated')}}</th>
            </tr>
        </thead>
        <tbody>
            <mod-row v-for="mod in mods" :key="mod.id" :mod="mod" :no-game="noGame" :sort="sortBy" :display-mode="displayMode"/>
        </tbody>
    </table>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';

defineProps<{
    displayMode: number,
    sortBy: string,
    noGame: boolean,
    error: true|Error,
    mods: Mod[]
}>();
</script>