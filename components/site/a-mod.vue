<template>
    <flex column gap="0" class="mod self-start !p-0" :title="mod.short_desc">
        <NuxtLink class="block" :to="link">
            <mod-thumbnail :mod="mod"/>
        </NuxtLink>
        <flex gap="1" column class="p-2 text-secondary">
            <NuxtLink class="mod-title" :to="link" :title="mod.name">
                <mod-status :mod="mod"/>
                {{mod.name}}
            </NuxtLink>

            <div>
                <a-user avatar-size="xs" :static="static" class="text-secondary" :user="mod.user"/>
            </div>

            <div v-if="!noCategories && ((mod.game && showGame) || mod.category)">
                <font-awesome-icon icon="map-marker-alt"/> <NuxtLink v-if="showGame" class="text-secondary" :to="!static && `/g/${mod.game.short_name || mod.game.id}` || null" :title="mod.game">{{mod.game.name}}</NuxtLink>
                <template v-if="mod.category">
                    <span v-if="showGame" class="text-secondary"> / </span>
                    <NuxtLink class="text-secondary" :to="!static && `/g/${mod.game.short_name}?category=${mod.category_id}` || null" :title="mod.category.name">{{mod.category.name}}</NuxtLink>
                </template>
            </div>

            <flex>
                <div class="inline-block">
                    <font-awesome-icon icon="heart"/> <span id="likes">{{likes}}</span>
                </div>
                <div class="inline-block">
                    <font-awesome-icon icon="download"/> <span>{{downloads}}</span>
                </div>
                <div class="inline-block">
                    <font-awesome-icon icon="eye"/> <span>{{views}}</span>
                </div>
    
                <span v-if="date" class="inline-block ml-auto">
                    <font-awesome-icon icon="clock"/> <time-ago :time="date"/>
                </span>
            </flex>
        </flex>
    </flex>
</template>
<script setup lang="ts">
import { Mod } from "~~/types/models";

const props = defineProps<{
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

const link = computed(() => !props.static ? `/mod/${props.mod.id}` : null);
</script>

<style>
.mod-title {
    font-size: 1.2rem;
    overflow: hidden;
    word-break: break-word;
    max-height: 60%;
    width: 100%;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.mod {
    width: 100%;
    min-height: 220px;
    justify-content: flex-start;
}
</style>