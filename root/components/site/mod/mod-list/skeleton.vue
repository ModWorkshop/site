<template>
    <div v-if="currentDisplayMode == 0" class="mods mods-grid gap-2">
        <div v-if="error">{{$t('error_fetching_mods')}}</div>
        <template v-else-if="mods">
            <grid-mod 
                v-for="[i, mod] in mods.entries()"
                :key="mod.id"
                :mod="mod"
                :lazy-thumbnail="i > 7"
                :game="game"
                :no-game="noGame"
                :sort="sortBy"
            />
        </template>
    </div>
    <m-table v-else>
        <template #head>
            <th v-if="currentDisplayMode == 1">{{$t('thumbnail')}}</th>
            <th>{{$t('name')}}</th>
            <th>{{$t('owner')}}</th>
            <th>{{!!noGame ? $t('category') : $t('game_category')}}</th>
            <th class="text-center">{{$t('likes')}}</th>
            <th class="text-center">{{$t('downloads')}}</th>
            <th class="text-center">{{$t('views')}}</th>
            <th class="text-center">{{sortBy == 'published_at' ? $t('published_at') : $t('last_updated')}}</th>
        </template>
        <template #body>
            <mod-row
                v-for="[i, mod] in mods?.entries()"
                :key="mod.id"
                :mod="mod"
                :lazy-thumbnail="i > 5"
                :no-game="noGame"
                :sort="sortBy"
                :display-mode="currentDisplayMode"
            />
        </template>
    </m-table>
</template>

<script setup lang="ts">
import type { Game, Mod } from '~~/types/models';
const savedDisplayMode = useConsentedCookie('mods-displaymode', { default: () => 0,  expires: longExpiration()});

const props = defineProps<{
    displayMode?: number,
    sortBy?: string,
    game?: Game,
    noGame: boolean,
    error?: Error|null,
    mods?: Mod[]
}>();

const currentDisplayMode = computed(() => props.displayMode ?? parseInt(savedDisplayMode.value as string));
</script>