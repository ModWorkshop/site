<template>
    <flex column gap="4">
        <a-input label="Name" v-model="mod.name" maxlength="150" minlength="3" desc="Maximum of 150 letters and minimum of 3 letters"/>

        <md-editor label="Description" desc="Describe your mod in detail here, what it does or any other important details" v-model="mod.desc" rows="12"/>

        <a-input label="Short Description" type="textarea" v-model="mod.short_desc" rows="2" maxlength="150" desc="Maximum of 150 letters. Will be shown in places like Discord, and when hovering mods"/>

        <group>
            <a-select label="Game" v-model="mod.game_id" placeholder="Select a game" :options="store.games.data" @update="refetchCats"/>
            <a-select label="Category" v-model="mod.category_id" placeholder="Select a category" :disabled="!mod.game_id" :options="mod.game_id && categories?.data"/>
        </group>

        <a-select v-model="mod.tag_ids" placeholder="Select tags" :options="tags.data" multiple label="Tags" desc="Make your mod more discoverable"/>

        <a-select label="Visiblity" v-model="mod.visibility" placeholder="Select a category" :options="visItems"/>

        <a-input label="Donation" desc="A donation link, currently supports PayPal, Ko-fi, and Patreon" v-model="mod.donation"/>

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

    const props = defineProps({
        mod: Object
    });

    const visItems = [
        { name: 'Public', value: 1 },
        { name: 'Hidden', value: 2 },
        { name: 'Unlisted', value: 3 },
        { name: 'Invite Only', value: 4 },
    ];

    const store = useStore();
    const { data: categories, refresh: refetchCats } = await useAsyncData('fetch-categories', () => useGet(`games/${props.mod.game_id}/categories`));
    const { data: tags } = await useAsyncData('getTagsAndGames', () => useGet('/tags'));
    await store.fetchGames();
</script>