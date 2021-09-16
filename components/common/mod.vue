<template>
    <div class="mod mx-auto" :title="mod.short_desc">
        <nuxt-link class="block ratio-image-mod-thumb" :to="`/mod/${mod.id}`">
            <mod-thumbnail :mod="mod"/>
        </nuxt-link>
        <div class="p-2 w-100 text-secondary" style="max-height:40%">
            <nuxt-link class="mod-title" :to="`/mod/${mod.id}`" :title="mod.name">
                <mod-status :mod="mod"/>
                {{mod.name}}
            </nuxt-link>

            <div>
                <font-awesome-icon icon="user"/>
                <user :avatar="false" class="text-secondary" :user="mod.submitter"/> <!--span>{{mod.collaborators.length}}</span-->
            </div>

            <div v-if="!noCategories && (mod.game || mod.category)">
                <font-awesome-icon icon="map-marker-alt"/>
                <nuxt-link v-if="mod.game" class="text-secondary" :to="`/game/${mod.game.short_name || mod.game.id}`" :title="mod.game">{{mod.game.name}}</nuxt-link>
                <template v-if="mod.category && mod.game_id != mod.category_id">
                    <span class="text-secondary"> / </span>
                    <nuxt-link class="text-secondary" :to="`/category/${mod.category_id}`" :title="mod.category.name">{{mod.category.name}}</nuxt-link>
                </template>
            </div>

            <div class="inline-block">
                <font-awesome-icon icon="heart"/>
                <span id="likes">{{likes}}</span>
            </div>
            <div class="inline-block">
                <font-awesome-icon icon="download"/>
                <span>{{downloads}}</span>
            </div>
            <div class="inline-block">
                <font-awesome-icon icon="eye"/>
                <span>{{views}}</span>
            </div>

            <span class="float-right">
                <span v-if="sort == 'pub_date'" :title="mod.pub_date">{{mod.timeago_pub}} <i class="ri-upload-2-fill"></i></span>
                <span v-else :title="mod.date"><i class="ri-time-fill"></i> {{mod.timeago}}</span>
            </span>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            sort: String,
            noCategories: Boolean,
            mod: Object
        },
        computed: {
            likes() {
                return 0;
            },
            downloads() {
                return this.mod.downloads;
            },
            views() {
                return this.mod.views;
            }
        }
    }
</script>
<style scoped>
    .mod-title {
        font-size:18px;
        overflow: hidden;
        word-break: break-word;
        max-height: 60%;
        width: 100%;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
</style>