<template>
    <tr class="items-center mt-2 content-block" :title="mod.short_desc">
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

        <td v-if="!lite && !noCategories">
            <template v-if="(mod.game && showGame) || mod.category">
                <NuxtLink v-if="showGame" class="text-secondary" :to="!static && gameUrl || null" :title="mod.game">{{mod.game.name}}</NuxtLink>
                <template v-if="mod.category">
                    <span v-if="showGame" class="text-secondary"> / </span>
                    <NuxtLink class="text-secondary" :to="!static && `${gameUrl}?category=${mod.category_id}` || null" :title="mod.category.name">{{mod.category.name}}</NuxtLink>
                </template>
            </template>
            <span v-else>
                {{$t('not_available')}}
            </span>
        </td>
        <td v-if="!lite">{{likes}}</td>
        <td v-if="!lite">{{downloads}}</td>
        <td v-if="!lite">{{views}}</td>
        <td v-if="!lite">
            <time-ago :time="date"/>
        </td>
        <slot name="definitions"/>
    </tr>
</template>

<script setup lang="ts">
import { useStore } from "~~/store";
import { Mod } from "~~/types/models";
const store = useStore();

const props = withDefaults(defineProps<{
    displayMode?: number,
    sort?: string,
    noCategories?: boolean,
    noGame?: boolean,
    mod: Mod,
    static?: boolean,
    lite?: boolean
}>(), { displayMode: 1, lite: false });

const showGame = computed(() => !props.noGame && props.mod.game);
const date = computed(() => props.sort == 'published_at' ? props.mod.published_at : props.mod.bumped_at);
const likes = computed(() => props.mod.likes);
const downloads = computed(() => props.mod.downloads);
const views = computed(() => props.mod.views);
const gameUrl = computed(() => `/g/${store.currentGame?.short_name || props.mod.game.short_name || props.mod.game.id}`);
</script>

<style scoped>

</style>