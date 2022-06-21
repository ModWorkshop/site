<template>
    <flex column gap="4">
        <group check="name" label="Name" desc="Maximum of 150 letters and minimum of 3 letters">
            <a-input v-model="mod.name" maxlength="100" minlength="3"/>
        </group>
        <group label="Description" label-id="desc" desc="Describe your mod in detail here, what it does or any other important details">
            <md-editor v-model="mod.desc" rows="12"/>
        </group>
        <group label="Short Description" desc="Maximum of 150 letters. Will be shown in places like Discord, and when hovering mods">
            <a-input type="textarea" v-model="mod.short_desc" rows="2" maxlength="150"/>
        </group>
        <group :labels="['Game', 'Category']">
            <a-select v-model="mod.game_id" placeholder="Select a game" :options="games"/>
            <a-select v-model="mod.category_id" placeholder="Select a category" :options="categories"/>
        </group>
        <group label="Tags" desc="Make your mod more discoverable">
            <a-select v-model="mod.tag_ids" placeholder="Select tags" :options="tags" multiple/>
        </group>
        <group label="Visiblity">
            <a-select v-model="mod.visibility" placeholder="Select a category" :options="visItems"/>
        </group>
        <group label="Donation" desc="A donation link, currently supports PayPal, Ko-fi, and Patreon">
            <a-input v-model="mod.donation"/>
        </group>
    
        <details>
            <summary>{{$t('license')}}</summary>
            <flex class="mt-3" column gap="2">
                <small><a href="https://choosealicense.com/" target="_blank">Can't choose?</a></small>
                <md-editor v-model="mod.license" rows="6"/>
            </flex>
        </details>
    </flex>
</template>

<script setup>
    import { useStore } from '~~/store';

    const { mod } = defineProps({
        mod: Object
    });

    const visItems = [
        { name: 'Public', value: 1 },
        { name: 'Hidden', value: 2 },
        { name: 'Unlisted', value: 3 },
        { name: 'Invite Only', value: 4 },
    ];

    const store = useStore();
    const { data: categories, refresh: refetchCats } = await useAPIFetch(`/games/${mod.game_id}/categories?include_paths=1`);
    const games = computed(() => store.games);
    const { data: tags } = await useAsyncData('getTagsAndGames', async () => {
        await store.fetchGames();
        return await useGet('/tags');
    });

    watch(() => mod.game_id, async () => {
        if (mod.game_id) {
            try {
                refetchCats();
            } catch (e) {
                console.log(e);
            }
        } else {
            categories.value = [];
        }
    }, {immediate: true});
</script>