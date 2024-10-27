<template>
    <m-flex class="mod p-4 content-block items-center" :title="mod.short_desc" wrap>
        <m-flex gap="2">
            <NuxtLink v-if="displayMode == 1" class="mod-thumbnail" :to="!static && `/mod/${mod.id}` || undefined">
                <mod-thumbnail :thumbnail="mod.thumbnail" :lazy="lazyThumbnail"/>
            </NuxtLink>
            <m-flex column gap="2">
                <NuxtLink class="text-lg" :to="!static && `/mod/${mod.id}` || undefined" :title="mod.name">
                    <mod-status :mod="mod"/>
                    {{mod.name}}
                </NuxtLink>
                
                <m-flex class="items-center" wrap>
                    <i18n-t :keypath="!noCategories && ((mod.game && showGame) || mod.category) ? 'user_posted_in_category' : 'user_posted'">
                        <template #user>
                            <a-user :static="static" class="text-secondary" avatar-size="xs" :user="mod.user"/>
                        </template>
                        <template #timeAgo>
                            <m-time-ago :time="date"/>
                        </template>
                        <template #place>
                            <m-flex wrap gap="0">
                                <NuxtLink v-if="showGame && mod.game" class="inline" :to="!static && gameUrl || undefined" :title="mod.game">
                                    {{mod.game.name}}
                                </NuxtLink>
                                <template v-if="mod.category">
                                    <i-mdi-menu-right v-if="showGame"/>
                                    <NuxtLink class="inline" :to="!static && `${gameUrl}/mods?category=${mod.category_id}` || undefined" :title="mod.category.name">{{mod.category.name}}</NuxtLink>
                                </template>
                            </m-flex>
                        </template>
                    </i18n-t>
                </m-flex>
    
                <m-flex class="items-center" gap="2">
                    <m-flex v-if="tags.length">
                        <NuxtLink v-for="tag in tags" :key="tag.id" :to="`${gameUrl}/mods}?selected-tags=${tag.id}`">
                            <m-tag :color="tag.color" small>{{tag.name}}</m-tag>
                        </NuxtLink>
                    </m-flex>
                </m-flex>
            </m-flex>
        </m-flex>

        <m-flex class="ml-auto">
            <m-flex gap="2" wrap>
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
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import { useStore } from "~~/store";
import type { Mod } from "~~/types/models";
const store = useStore();

const { mod, noGame, displayMode = 1, sort } = defineProps<{
    displayMode?: number,
    lazyThumbnail?: boolean,
    sort?: string,
    noCategories?: boolean,
    noGame?: boolean,
    mod: Mod,
    static?: boolean,
}>();

const showGame = computed(() => !noGame && mod.game);
const date = computed(() => sort == 'published_at' ? mod.published_at : mod.bumped_at);
const gameUrl = computed(() => `/g/${store.currentGame?.short_name || mod.game?.short_name || mod.game?.id}`);
const locale = computed(() => useI18n().locale.value);
const tags = computed(() => {
    if (!mod.tags) {
        return [];
    }
    return mod.tags.slice(0, 3);
});

const likes = computed(() => shortStat(mod.likes));
const downloads = computed(() => shortStat(mod.downloads));
const views = computed(() => shortStat(mod.views));
const fullLikes = computed(() => friendlyNumber(locale.value, mod.likes));
const fullDownloads = computed(() => friendlyNumber(locale.value, mod.downloads));
const fullViews = computed(() => friendlyNumber(locale.value, mod.views));
</script>

<style scoped>
.mod-title {
    overflow: hidden;
    word-break: break-word;
    white-space: pre-wrap;
}

.mod {
    font-size: 13px;
}

.mod-thumbnail {
    width: 200px;
}

@media (max-width:768px) {
    .mod-thumbnail {
        width: 150px;
    }
}
</style>