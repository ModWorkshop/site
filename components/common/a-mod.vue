<template>
    <content-block gap="0" class="mod self-start !p-0" :title="mod.short_desc">
        <nuxt-link class="block ratio-image-mod-thumb" :to="`/mod/${mod.id}`">
            <mod-thumbnail :mod="mod"/>
        </nuxt-link>
        <flex gap="1" column class="p-2 text-secondary">
            <nuxt-link class="mod-title" :to="`/mod/${mod.id}`" :title="mod.name">
                <mod-status :mod="mod"/>
                {{mod.name}}
            </nuxt-link>

            <div>
                <font-awesome-icon icon="user"/> <a-user :avatar="false" class="text-secondary" :user="mod.submitter"/> <!--span>{{mod.collaborators.length}}</span-->
            </div>

            <div v-if="!noCategories && ((mod.game && showGame) || mod.category)">
                <font-awesome-icon icon="map-marker-alt"/> <nuxt-link v-if="showGame" class="text-secondary" :to="`/game/${mod.game.short_name || mod.game.id}`" :title="mod.game">{{mod.game.name}}</nuxt-link>
                <template v-if="mod.category">
                    <span v-if="showGame" class="text-secondary"> / </span>
                    <nuxt-link class="text-secondary" :to="`/category/${mod.category_id}`" :title="mod.category.name">{{mod.category.name}}</nuxt-link>
                </template>
            </div>

            <flex gap="1">
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
                    <span :title="date">
                        <font-awesome-icon icon="clock"/> {{timeAgoText}}
                    </span>
                </span>
            </flex>
        </flex>
    </content-block>
</template>
<script setup lang="ts">
import { Mod } from "~~/types/models";
import { timeAgo } from "../../utils/helpers";

const props = defineProps<{
    sort?: string,
    noCategories?: boolean,
    noGame?: boolean,
    mod: Mod
}>();

const showGame = computed(() => !props.noGame && props.mod.game);
const date = computed(() => props.sort == 'publish_date' ? props.mod.publish_date : props.mod.bump_date);
const timeAgoText = computed(() => timeAgo(date.value));
const likes = computed(() => props.mod.likes);
const downloads = computed(() => props.mod.downloads);
const views = computed(() => props.mod.views);

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