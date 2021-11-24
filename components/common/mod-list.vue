<template>
    <flex column gap="3">
        <content-block :column="false">
            <group label="Search" class="flex-grow">
                <el-input type="text" v-model="query"/>
            </group>
            <group label="Game">
                <el-select placeholder="Select game" filterable>
                    
                </el-select>
            </group>
            <group label="Categories">
                <el-select placeholder="Select categories" clearable multiple filterable>
                    
                </el-select>
            </group>
            <group label="Tags">
                <el-select v-model="selectedTags" placeholder="Select Tags" clearable multiple filterable collapse-tags>
                    <el-option v-for="tag in tags" :key="tag.id" :label="tag.name" :value="tag.id"/>
                </el-select>
            </group>
            <group label="Filter Out Tags">
                <el-select v-model="selectedBlockTags" placeholder="Select Tags" clearable multiple filterable collapse-tags>
                    <el-option v-for="tag in tags" :key="tag.id" :label="tag.name" :value="tag.id"/>
                </el-select>
            </group>
                <a-button color="none" icon="ellipsis-v"/>
        </content-block>
        <flex gap="3" style="justify-content: end;">
            <el-select value="1">
                <el-option label="Last Updated" value="1"/>
                <el-option label="Popularity" value="2"/>
                <el-option label="Likes" value="3"/>
                <el-option label="Downloads" value="4"/>
                <el-option label="Views" value="5"/>
                <el-option label="Name" value="6"/>
                <el-option label="Publish Date" value="7"/>
            </el-select>
            <flex gap="1">
                <a-button icon="th" style="background-color: var(--tab-selected-color)"/>
                <a-button icon="list"/>
                <a-button icon="bars"/>
            </flex>
        </flex>

        <flex column>
            <h4 v-if="title" class="text-center my-3 text-primary">{{title}}</h4>
            <flex wrap column gap="3" class="mods justify-content-start">
                <div v-if="isList" id="mod_list_head" class="p-3 list_mod align-items-center content-bg" style="height:40px;">
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
                <div id="content" :class="`mods ${isList ? 'mods-list' : 'mods-grid'}`">
                    <a-mod v-for="mod in mods" :key="mod.id" :mod="mod"/>
                </div>
                <a-button v-if="hasMore" id="load-more" color="none" icon="chevron-down" @click="() => incrementPage()">{{$t('load_more')}}</a-button>
                <span v-else-if="mods.length == 0" class="text-center">
                    No mods found
                </span>
            </flex>
        </flex>
    </flex>
</template>
<script setup>
    import { useFetch, watch, ref, useContext, computed } from '@nuxtjs/composition-api';
    import { useStore } from '../../store';

    const props = defineProps({
        title: String,
        userId: Number
    });

    const store = useStore();

    const query = ref('');
    const page = ref(1);
    const isList = ref(false);
    const justDate = ref(false);
    const hasMore = ref(false);
    const selectedTags = ref([]);
    const selectedBlockTags = ref([]);
    const tags = computed(() => store.tags);
    const { $ftch } = useContext();
    const mods = ref([]);
    useFetch(async () => {
        await loadMods();
        await store.fetchTags();
    });

    let lastTimeout = null;
    watch([query, selectedTags, selectedBlockTags], () => {
        if (lastTimeout) {
            clearTimeout(lastTimeout);
            lastTimeout = null;
        }
        lastTimeout = setTimeout(loadMods, 250);
    });

    function incrementPage() {
        setPage(page.value++, false);
    }

    function setPage(page, reload) {
        page.value = page;
        loadMods(reload);
    }

    async function getMods() {
        const paginator = await $ftch.get('mods', { 
            params: { 
                page: page.value,
                query: query.value,
                submitter_id: props.userId,
                tags: selectedTags.value,
                block_tags: selectedBlockTags.value,
            }
        });

        hasMore.value = parseInt(paginator.meta.total / 40) > 0;

        return paginator.data;
    }

    async function loadMods(reload=true) {
        const retMods = await getMods();
        if (reload) {
            mods.value = retMods;
        } else {
            mods.value = [...mods.value, ...retMods];
        }
    }
</script>