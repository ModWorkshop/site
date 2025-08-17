<template>
    <mod-list-skeleton 
        :display-mode="displayMode"
        :sort-by="sortBy"
        :no-game="!!forcedGame"
        :error="error"
        :mods="fetchedMods?.data"
    />
    <span v-if="!fetchedMods?.data.length" class="text-center">{{$t('no_mods_found')}}</span>
</template>
<script setup lang="ts">
import type { SearchParameters } from 'ofetch';
import type { Mod } from '~/types/models';

const props = defineProps<{
    forcedGame?: number,
    params?: SearchParameters
}>();

const sortBy = computed(() => props.params?.sort);

const displayMode = useConsentedCookie('mods-displaymode', { default: () => 0, expires: longExpiration() });

const { data: fetchedMods, error } = await useFetchMany<Mod>('mods', { params: props.params });
</script>