<template>
    <tr class="items-center mt-2" :title="mod.short_desc">
        <td v-if="displayMode == 1" width="200px;">
            <NuxtLink class="block" :to="!static && `/mod/${mod.id}` || null">
                <mod-thumbnail :thumbnail="mod.thumbnail"/>
            </NuxtLink>
        </td>

        <td>
            <NuxtLink class="mod-title" :to="!static && `/mod/${mod.id}` || null" :title="mod.name">
                <mod-status :mod="mod"/>
                {{mod.name}}
            </NuxtLink>
        </td>

        <td>
            <a-user :static="static" class="text-secondary" :user="mod.user"/>
        </td>

        <td v-if="!noCategories">
            <template v-if="(mod.game && showGame) || mod.category">
                <NuxtLink v-if="showGame" class="text-secondary" :to="!static && `/g/${mod.game.short_name || mod.game.id}` || null" :title="mod.game">{{mod.game.name}}</NuxtLink>
                <template v-if="mod.category">
                    <span v-if="showGame" class="text-secondary"> / </span>
                    <NuxtLink class="text-secondary" :to="!static && `/g/${mod.game.short_name}?category=${mod.category_id}` || null" :title="mod.category.name">{{mod.category.name}}</NuxtLink>
                </template>
            </template>
            <span v-else>
                {{$t('na')}}
            </span>
        </td>
        <td>{{likes}}</td>
        <td>{{downloads}}</td>
        <td>{{views}}</td>
        <td>
            <time-ago :time="date"/>
        </td>
    </tr>
</template>

<script setup lang="ts">
import { Mod } from "~~/types/models";

const props = defineProps<{
    displayMode: number,
    sort?: string,
    noCategories?: boolean,
    noGame?: boolean,
    mod: Mod,
    static?: boolean
}>();

const showGame = computed(() => !props.noGame && props.mod.game);
const date = computed(() => props.sort == 'published_at' ? props.mod.published_at : props.mod.bumped_at);
const likes = computed(() => props.mod.likes);
const downloads = computed(() => props.mod.downloads);
const views = computed(() => props.mod.views);
</script>

<style scoped>

</style>