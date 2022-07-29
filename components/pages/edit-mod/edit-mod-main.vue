<template>
    <flex column gap="4">
        <a-input v-model="mod.name" label="Name" maxlength="150" minlength="3" required desc="Maximum of 150 letters and minimum of 3 letters"/>

        <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

        <a-input v-model="mod.short_desc" label="Short Description" type="textarea" rows="2" maxlength="150" desc="Maximum of 150 letters. Will be shown in places like Discord, and when hovering mods"/>

        <flex>
            <a-select v-model="mod.game_id" label="Game" placeholder="Select a game" :options="store.games.data" @update="refetchCats"/>
            <a-select v-model="mod.category_id" label="Category" placeholder="Select a category" :disabled="!mod.game_id" :options="mod.game_id && categories?.data"/>
        </flex>

        <a-select v-model="mod.tag_ids" placeholder="Select tags" :options="tags.data" multiple label="Tags" desc="Make your mod more discoverable"/>

        <a-select v-model="mod.visibility" label="Visiblity" placeholder="Select a category" :options="visItems"/>

        <a-input v-model="mod.donation" label="Donation" desc="A donation link, currently supports PayPal, Ko-fi, and Patreon"/>

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
        { name: 'Unlisted', value: 3 }
    ];

    const store = useStore();
    const { data: categories, refresh: refetchCats } = await useAsyncData('fetch-categories', () => useGet(`games/${props.mod.game_id}/categories`));
    const { data: tags } = await useAsyncData('getTagsAndGames', () => useGet('/tags'));
    await store.fetchGames();
</script>