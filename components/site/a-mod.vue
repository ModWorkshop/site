<template>
    <flex column gap="0" class="mod content-block" :title="mod.short_desc">
        <NuxtLink :to="link">
            <mod-thumbnail :thumbnail="mod.thumbnail"/>
        </NuxtLink>
        <flex gap="1" column class="p-2 text-secondary place-content-around" grow>
            <NuxtLink class="mod-title" :to="link" :title="mod.name">
                <mod-status :mod="mod"/> {{mod.name}}
            </NuxtLink>

            <a-user avatar-size="xs" :static="static" class="text-secondary" :user="mod.user"/>

            <template v-if="!noCategories">
                <div v-if="((mod.game && showGame) || mod.category)" style="">
                    <a-icon class="mr-1" name="mdi:map-marker"/> 
                    <NuxtLink v-if="showGame && mod.game" class="text-secondary inline" :to="!static && gameUrl || undefined" :title="mod.game">
                        {{mod.game.name}}
                    </NuxtLink>
                    <template v-if="mod.category">
                        <template v-if="showGame"> / </template>
                        <NuxtLink class="text-secondary inline" :to="!static && `${gameUrl}?category=${mod.category_id}` || undefined" :title="mod.category.name">{{mod.category.name}}</NuxtLink>
                    </template>
                </div>
            </template>

            <flex>
                <span :title="mod.likes.toString()">
                    <a-icon icon="mdi:heart"/> {{likes}}
                </span>
                <span :title="mod.downloads.toString()">
                    <a-icon icon="mdi:download"/> {{downloads}}
                </span>
                <span :title="mod.views.toString()">
                    <a-icon icon="mdi:eye"/> {{views}}
                </span>
                <span v-if="date" class="inline-block ml-auto">
                    <a-icon icon="mdi:clock"/> <time-ago :time="date"/>
                </span>
            </flex>
        </flex>
    </flex>
</template>
<script setup lang="ts">
import { useStore } from "~~/store";
import { Game, Mod } from "~~/types/models";

const store = useStore();

const props = defineProps<{
    sort?: string,
    noCategories?: boolean,
    noGame?: boolean,
    mod: Mod,
    game: Game,
    static?: boolean
}>();

const showGame = computed(() => !props.noGame && props.mod.game);
const date = computed(() => props.sort == 'published_at' ? props.mod.published_at : props.mod.bumped_at);
const likes = computed(() => shortStat(props.mod.likes));
const downloads = computed(() => shortStat(props.mod.downloads));
const views = computed(() => shortStat(props.mod.views));

const link = computed(() => !props.static ? `/mod/${props.mod.id}` : undefined);
const gameUrl = computed(() => `/g/${props.game?.short_name || store.currentGame?.short_name || props.mod.game?.short_name || props.mod.game?.id}`);
</script>

<style scoped>
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