<template>
    <flex column>
        <h4 v-if="title" class="text-center my-3 text-primary">{{title}}</h4>
        <flex wrap column class="mods justify-content-start">
            <div v-if="isList" id="mod_list_head" class="p-3 list_mod align-items-center bg-dark" style="height:40px;">
                <div id="thumbnail" class="{% if cookies.mods_displaymode == 3 %} d-none{% endif %}" style="min-width: 200px;"></div>
                <div class="ml-2" style="flex: 4;">{{$t('mod_name')}}</div>
                <div style="flex: 3">{{$t('author')}}</div>
                <div v-if="type != 3" style="flex: 3">{{type == 2 ? $t('category') : $t('game_category')}}</div>
                <div>{{$t('likes')}}</div>
                <div>{{$t('downloads')}}</div>
                <div>{{$t('download_views')}}</div>
                <div v-if="justDate" style="flex: 2;">{{$t('date')}}</div>
                <template v-else>
                    <div id="date" style="flex: 2;">{{$t('last_updated')}}</div>
                    <div id="pub-date" class="d-none" style="flex: 2;">{{$t('publish_date')}}</div>
                </template>
            </div>
            <div id="content" :class="`mods p-3 ${isList ? 'mods-list' : 'mods-grid'}`">
                <a-mod v-for="mod in mods" :key="mod.id" :mod="mod"/>
            </div>
            <button id="load-more" class="btn" @click="loadMods">{{$t('load_more')}}</button>
        </flex>
    </flex>
</template>
<script setup>
    import { useAsync, useContext } from '@nuxtjs/composition-api';
    defineProps({
        title: String
    });

    let isList = $ref(false);
    let justDate = $ref(false);
    const { $factory, route } = useContext();
    const mods = useAsync(() => $factory.get('mods'), route.value.path);

    async function loadMods() {
        mods.value = [...mods.value, ...await this.$factory.get('mods')];
    }
</script>