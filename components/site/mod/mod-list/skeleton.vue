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
    <m-flex v-else column>
        <list-mod
            v-for="[i, mod] in mods?.entries()"
            :key="mod.id"
            :mod="mod"
            :lazy-thumbnail="i > 5"
            :no-game="noGame"
            :sort="sortBy"
            :display-mode="currentDisplayMode"
        />
    </m-flex>
</template>

<script setup lang="ts">
import type { Game, Mod } from '~~/types/models';
const savedDisplayMode = useConsentedCookie<number>('mods-displaymode', { default: () => 0,  expires: longExpiration() });

const props = defineProps<{
    displayMode?: number,
    sortBy?: string,
    game?: Game,
    noGame: boolean,
    error?: Error|null,
    mods?: Mod[]
}>();

const currentDisplayMode = computed(() => props.displayMode ?? savedDisplayMode.value);
</script>