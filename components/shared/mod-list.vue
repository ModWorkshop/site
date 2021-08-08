<template>
    <flex column class="content-block-huge">
        <h4 v-if="title" class="text-center my-3 text-primary">{{title}}</h4>
        <div class="mods d-flex flex-wrap justify-content-start flex-column">
            <div v-if="is_list" id="mod_list_head" class="p-3 list_mod align-items-center bg-dark" style="height:40px;">
                <div id="thumbnail" class="{% if cookies.mods_displaymode == 3 %} d-none{% endif %}" style="min-width: 200px;"></div>
                <div class="ml-2" style="flex: 4;">{{lang.mod_name}}</div>
                <div style="flex: 3">{{lang.mydownloads_author}}</div>
                <div v-if="type != 3" style="flex: 3">{{type == 2 ? lang.category : lang.game_category}}</div>
                <div>{{lang.likes}}</div>
                <div>{{lang.mydownloads_downloads}}</div>
                <div>{{lang.mydownloads_download_views}}</div>
                <div v-if="just_date" style="flex: 2;">{{lang.mydownloads_date}}</div>
                <template v-else>
                    <div id="date" style="flex: 2;">{{lang.mydownloads_last_updated}}</div>
                    <div id="pub-date" class="d-none" style="flex: 2;">{{lang.publish_date}}</div>
                </template>
            </div>
            <div id="content" :class="`mods p-3 ${is_list ? 'mods-list' : 'mods-grid'}`">
                <mod v-for="mod in mods" :key="mod.id" :mod="mod"/>
            </div>
            <button id="loadmore" class="btn" type="button" style="display:none;">{{lang.load_more}}</button>
        </div>
    </flex>
</template>
<script>
export default {
    props: {
        mods: Array
    },
    data() {
        return {
            lang: {
            },
            is_list: false
        }
    }
}
</script>