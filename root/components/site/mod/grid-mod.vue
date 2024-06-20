<template>
    <div class="mod content-block" :title="mod.short_desc">
        <NuxtLink :to="link">
            <mod-thumbnail :thumbnail="mod.thumbnail" :lazy="lazyThumbnail"/>
        </NuxtLink>
        <div class="mod-details">
            <NuxtLink class="mod-title" :to="link" :title="mod.name">
                <mod-status :mod="mod"/> {{mod.name}}
            </NuxtLink>

            <a-user avatar-size="xs" :static="static" class="text-secondary" :user="mod.user"/>

            <template v-if="!noCategories">
                <div v-if="((mod.game && showGame) || mod.category)" style="">
                    <i-mdi-map-marker class="mr-1"/> 
                    <NuxtLink v-if="showGame && mod.game" class="text-secondary inline" :to="!static && gameUrl || undefined" :title="mod.game">
                        {{mod.game.name}}
                    </NuxtLink>
                    <template v-if="mod.category">
                        <template v-if="showGame"> / </template>
                        <NuxtLink class="text-secondary inline" :to="!static && `${gameUrl}/mods?category=${mod.category_id}` || undefined" :title="mod.category.name">{{mod.category.name}}</NuxtLink>
                    </template>
                </div>
            </template>

            <m-flex>
                <span v-if="date" class="inline-block">
                    <i-mdi-clock/> <m-time-ago :time="date"/>
                </span>
            </m-flex>

            <m-flex>
                <span :title="fullLikes">
                    <i-mdi-heart/> {{likes}}
                </span>
                <span :title="fullDownloads">
                    <i-mdi-download/> {{downloads}}
                </span>
                <span :title="fullViews">
                    <i-mdi-eye/> {{views}}
                </span>
            </m-flex>
        </div>
    </div>
</template>
<script setup lang="ts">
import { useStore } from "~~/store";
import type { Game, Mod } from "~~/types/models";

const store = useStore();

const props = defineProps<{
    sort?: string,
    lazyThumbnail?: boolean,
    noCategories?: boolean,
    noGame?: boolean,
    mod: Mod,
    game?: Game,
    static?: boolean
}>();

const i18n = useI18n();
const locale = computed(() => i18n.locale.value);
const showGame = computed(() => !props.noGame && props.mod.game);
const date = computed(() => props.sort == 'published_at' ? props.mod.published_at : props.mod.bumped_at);
const likes = computed(() => shortStat(props.mod.likes));
const downloads = computed(() => shortStat(props.mod.downloads));
const views = computed(() => shortStat(props.mod.views));

const fullLikes = computed(() => friendlyNumber(locale.value, props.mod.likes));
const fullDownloads = computed(() => friendlyNumber(locale.value, props.mod.downloads));
const fullViews = computed(() => friendlyNumber(locale.value, props.mod.views));

const link = computed(() => !props.static ? `/mod/${props.mod.id}` : undefined);
const gameUrl = computed(() => `/g/${props.game?.short_name || store.currentGame?.short_name || props.mod.game?.short_name || props.mod.game?.id}`);
</script>

<style scoped>
.mod-title {
    font-size: 1.15rem;
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
    display: flex;
    gap: 0;
    flex-direction: column;
    justify-content: flex-start;
}

.mod-details {
    padding: 0.5rem 0.75rem;
    color: var(--secondary-text-color);
    place-content: space-around;
    display: flex;
    gap: 4px;
    flex: 1;
    flex-direction: column;
    word-break: break-word;
}
</style>