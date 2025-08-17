<template>
    <div class="mod content-block" :title="mod.short_desc">
        <NuxtLink :to="link">
            <mod-thumbnail class="card-thumbnail" :thumbnail="mod.thumbnail" :lazy="lazyThumbnail"/>
        </NuxtLink>
        <div class="mod-details">
            <NuxtLink class="card-title" :to="link" :title="mod.name">
                <mod-status :mod="mod" class="text-lg"/> {{mod.name}}
            </NuxtLink>

            <m-flex class="items-center gap-1" wrap>
                <a-user avatar-size="xs" :static="static" class="text-secondary" :user="mod.user"/> 
            </m-flex>

            <m-flex class="items-center" wrap v-if="!noCategories && ((mod.game && showGame) || mod.category)" gap="0">
                <NuxtLink v-if="showGame && mod.game" class="text-secondary inline" :to="!static && gameUrl || undefined" :title="mod.game.name">
                    {{mod.game.name}}
                </NuxtLink>
                <template v-if="mod.category">
                    <i-mdi-menu-right v-if="showGame" style="margin-bottom: -0.05rem;" />
                    <NuxtLink class="text-secondary inline" :to="!static && `${gameUrl}/mods?category=${mod.category_id}` || undefined" :title="mod.category.name">{{mod.category.name}}</NuxtLink>
                </template>
            </m-flex>

            <m-flex v-if="tags.length" wrap>
                <NuxtLink v-for="tag in tags" :key="tag.id" :to="`${gameUrl}/mods?selected-tags=${tag.id}`">
                    <m-tag :color="tag.color" small>{{tag.name}}</m-tag>
                </NuxtLink>
            </m-flex>

            <m-flex class="items-center mt-auto" wrap>
                <m-time class="mr-auto" :datetime="date" relative/>

                <m-flex gap="1">
                    <span :title="fullLikes">
                        <i-mdi-heart style="margin-right: 2px;"/>{{likes}}
                    </span>
                    <span :title="fullDownloads">
                        <i-mdi-download style="margin-right: 2px;"/>{{downloads}}
                    </span>
                    <span :title="fullViews">
                        <i-mdi-eye style="margin-right: 2px;"/>{{views}}
                    </span>
                </m-flex>
            </m-flex>
        </div>
    </div>
</template>
<script setup lang="ts">
import { useStore } from "~/store";
import type { Game, Mod } from "~/types/models";

const store = useStore();

const { mod, game, static: isStatic, noGame, sort } = defineProps<{
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
const showGame = computed(() => !noGame && mod.game);
const date = computed(() => sort == 'published_at' ? mod.published_at : mod.bumped_at);
const likes = computed(() => shortStat(mod.likes));
const downloads = computed(() => shortStat(mod.downloads));
const views = computed(() => shortStat(mod.views));

const tags = computed(() => {
    if (!mod.tags) {
        return [];
    }
    return mod.tags.slice(0, 4);
});

const fullLikes = computed(() => friendlyNumber(locale.value, mod.likes));
const fullDownloads = computed(() => friendlyNumber(locale.value, mod.downloads));
const fullViews = computed(() => friendlyNumber(locale.value, mod.views));

const link = computed(() => !isStatic ? `/mod/${mod.id}` : undefined);
const gameUrl = computed(() => `/g/${game?.short_name || store.currentGame?.short_name || mod.game?.short_name || mod.game?.id}`);
</script>

<style scoped>
.mod {
    font-size: 13px;
    width: 100%;
    min-height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

.mod-details {
    padding: 1.5rem;
    color: var(--secondary-text-color);
    gap: 12px;
    display: flex;
    flex: 1;
    flex-direction: column;
    word-break: break-word;
}
</style>