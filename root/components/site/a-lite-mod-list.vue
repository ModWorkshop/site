<template>
    <flex column gap="3">
        <flex>
            <a-button class="mr-auto" icon="mdi:puzzle" :to="link">{{$t('browse_mods')}}</a-button>
            <button-group v-model:selected="displayMode" class="ml-auto mr-1 hidden md:flex" gap="1" button-style="button">
                <a-group-button icon="mdi:view-grid" :name="0"/>
                <a-group-button icon="mdi:view-list" :name="1"/>
                <a-group-button icon="mdi:view-headline" :name="2"/>
            </button-group>
        </flex>
        <flex class="p-2">
            <NuxtLink class="text-body h2 my-auto" :to="link">
                {{$t('popular_mods')}}🌟
            </NuxtLink>
        </flex>
        <flex column>
            <mod-list-skeleton
                sort-by="daily_score"
                :display-mode="displayMode"
                :no-game="!!game"
                :error="popularError"
                :game="game"
                :mods="popular?.data"
            />
        </flex>
        <flex class="p-2">
            <NuxtLink class="text-body h2 my-auto" :to="link">
                {{$t('latest_mods')}}
            </NuxtLink>
        </flex>
        <flex column>
            <mod-list-skeleton
                sort-by="latest"
                :display-mode="displayMode"
                :no-game="!!game"
                :error="latestError"
                :game="game"
                :mods="latest?.data"
            />
        </flex>
        <a-button color="subtle" icon="mdi:puzzle" :to="link">{{$t('browse_mods')}}</a-button>
    </flex>
</template>

<script setup lang="ts">
import { Game } from '~/types/models';
const displayMode = useConsentedCookie('mods-displaymode', { default: () => 0, expires: longExpiration()});

const props = defineProps<{
    game?: Game,
    link?: string,
}>();

const { data: latest, error: latestError } = await useFetchMany(props.game ? `games/${props.game.id}/mods` : 'mods', { 
    params: { 
        limit: 10,
        'fields[mods]': listModFields.join(','),
    }
});
const { data: popular, error: popularError } = await useFetchMany(props.game ? `games/${props.game.id}/mods` : 'mods', { 
    params: { 
        'fields[mods]': listModFields.join(','),
        sort: 'daily_score',
        limit: 5
    }
});
</script>
