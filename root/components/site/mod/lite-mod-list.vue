<template>
    <m-flex column gap="3">
        <m-flex>
            <m-button class="mr-auto" :to="link"><i-mdi-puzzle/> {{$t('browse_mods')}}</m-button>
            <m-toggle-group v-model:selected="displayMode" class="ml-auto mr-1 hidden md:flex" gap="1" button-style="button">
                <m-toggle-group-item :name="0"><i-mdi-view-grid/></m-toggle-group-item>
                <m-toggle-group-item :name="1"><i-mdi-view-list/></m-toggle-group-item>
                <m-toggle-group-item :name="2"><i-mdi-view-headline/></m-toggle-group-item>
            </m-toggle-group>
        </m-flex>
        <m-flex class="p-2">
            <NuxtLink class="text-body h2 my-auto" :to="link">
                {{$t('popular_mods')}}🌟
            </NuxtLink>
        </m-flex>
        <m-flex column>
            <mod-list-skeleton
                sort-by="daily_score"
                :display-mode="displayMode"
                :no-game="!!game"
                :error="error"
                :game="game"
                :mods="data?.popular"
            />
        </m-flex>
        <m-flex class="p-2">
            <NuxtLink class="text-body h2 my-auto" :to="link">
                {{$t('latest_mods')}}
            </NuxtLink>
        </m-flex>
        <m-flex column>
            <mod-list-skeleton
                sort-by="latest"
                :display-mode="displayMode"
                :no-game="!!game"
                :error="error"
                :game="game"
                :mods="data?.latest"
            />
        </m-flex>
        <m-button color="subtle" :to="link"><i-mdi-puzzle/> {{$t('browse_mods')}}</m-button>
    </m-flex>
</template>

<script setup lang="ts">
import type { Game, Mod } from '~/types/models';
const displayMode = useConsentedCookie('mods-displaymode', { default: () => 0, expires: longExpiration()});

const props = defineProps<{
    game?: Game,
    link?: string,
}>();

const { data, error } = await useFetchData<{ latest: Mod[], popular: Mod[] }>(props.game ? `games/${props.game.id}/popular-and-latest` : 'popular-and-latest');
</script>
