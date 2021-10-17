<template>
    <flex column gap="4">
        <group check="name" label="Name" desc="Maximum of 150 letters and minimum of 3 letters">
            <el-input v-model="mod.name" maxlength="100" minlength="3"/>
        </group>
        <group label="Description" label-id="desc" desc="Describe your mod in detail here, what it does or any other important details">
            <md-editor v-model="mod.desc" rows="12"/>
        </group>
        <group label="Short Description" desc="Maximum of 150 letters. Will be shown in places like Discord, and when hovering mods">
            <el-input type="textarea" v-model="mod.short_desc" rows="2" maxlength="150"/>
        </group>
        <group :labels="['Game', 'Category']">
            <el-select v-model="mod.game_id" placeholder="Select a game" filterable>
                <el-option v-for="game in games" :key="game.id" :label="game.name" :value="game.id"/>
            </el-select>
            <el-select v-model="mod.category_id" placeholder="Select a category" clearable filterable>
                <el-option v-for="category in categories" :key="category.id" :label="category.path" :value="category.id"/>
            </el-select>
        </group>
        <group label="Tags" desc="Make your mod more discoverable">
            <el-select v-model="mod.tag_ids" placeholder="Select tags" clearable multiple filterable>
                <el-option v-for="tag in tags" :key="tag.id" :label="tag.name" :value="tag.id"/>
            </el-select>
        </group>
        <group label="Visiblity">
            <el-select v-model="mod.visibility" placeholder="Select a category">
                <el-option label="Public" :value="1"/>
                <el-option label="Hidden" :value="2"/>
                <el-option label="Unlisted" :value="3"/>
                <el-option label="Invite Only" :value="4"/>
            </el-select>
        </group>
        <group label="Donation" desc="A donation link, currently supports PayPal, Ko-fi, and Patreon">
            <el-input v-model="mod.donation"/>
        </group>
    
        <details>
            <summary>{{$t('license')}}</summary>
            <flex class="mt-3" column gap="2">
                <small><a href="https://choosealicense.com/" target="_blank">Can't choose?</a></small>
                <md-editor v-model="mod.license" rows="6"/>
            </flex>
        </details>

        <group>
            <div>
                <el-checkbox v-model="mod.is_nsfw">NSFW Mod</el-checkbox>
                <br>
                <small>If the mod contains adult content you must set your mod as NSFW. Read the rules regarding NSFW or risk getting banned</small>
            </div>
        </group>
    </flex>
</template>

<script setup>
    import { useFetch } from '@nuxtjs/composition-api';
    import { useStore } from '~~/store';

    const props = defineProps({
        modData: Object
    });

    const { $axios } = useNuxtApp().legacyApp;
    const store = useStore();
    const mod = computed(() => props.modData);

    const categories = ref([]);
    const tags = ref([]);
    const games = computed(() => store.games);
    useFetch(async () => {
        await store.fetchGames();
        const { data: tags } = await $axios.get('/tags');
        tags.value = tags;
    });

    watch(() => mod.value.game_id, async () => {
        if (mod.value.game_id) {
            try {
                categories.value = await $axios.get(`/games/${mod.value.game_id}/categories?include_paths=1`).then(res => res.data);
            } catch (e) {
                console.log(e);
            }
        } else {
            categories.value = [];
        }
    }, {immediate: true});
</script>