<template>
    <div class="mod mx-auto" :title="mod.short_desc">
        <a class="block ratio-image-mod-thumb" :href="`/mod/${mod.id}`">
            <mod-thumbnail :mod="mod"/>
        </a>
        <div class="p-2 w-100 text-gray" style="max-height:40%">
            <a class="mod-title" :href="`/mod/${mod.id}`" :title="mod.name">
                <mod-status :mod="mod"/>
                {{mod.name}}
            </a>

            <div>
                <i class="ri-user-3-fill"></i> <user class="text-gray" :user="mod.submitter"/> <!--span>{{mod.collaborators.length}}</span-->
            </div>

            <div v-if="!noCategories">
                <i class="ri-map-pin-2-fill"></i> 
                <a class="text-gray" :href="`/game/${mod.game_short || mod.gid}`" :title="mod.game">{{mod.game}}</a>
                <template v-if="mod.gid != mod.cid">
                    <span class="text-secondary"> / </span>
                    <a class="text-gray" :href="`/category/${mod.cid}`" :title="mod.category">{{mod.category}}</a>
                </template>
            </div>

            <span><i class="ri-heart-fill"></i> {{mod.likes}}</span>
            <span><i class="ri-download-fill"></i> {{mod.downloads}}</span>
            <span><i class="ri-eye-fill"></i> {{mod.views}}</span>
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