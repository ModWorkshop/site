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
import { DateTime } from 'luxon';
import { SearchParams } from 'ohmyfetch';
import { Mod } from '~~/types/models';

const props = defineProps<{
    forcedGame?: number,
    params?: SearchParams
}>();

const sortBy = computed(() => props.params.sort_by);

const displayMode = useCookie('mods-displaymode', { default: () => 0, expires: DateTime.now().plus({ years: 99 }).toJSDate()});

const { data: fetchedMods, error } = await useFetchMany<Mod>('mods', { params: props.params });
</script>