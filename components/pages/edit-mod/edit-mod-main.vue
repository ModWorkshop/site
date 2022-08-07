<template>
    <flex column gap="4">
        <a-input v-model="mod.name" label="Name" maxlength="150" minlength="3" required desc="Maximum of 150 letters and minimum of 3 letters"/>

        <md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

        <a-input v-model="mod.short_desc" label="Short Description" type="textarea" rows="2" maxlength="150" desc="Maximum of 150 letters. Will be shown in places like Discord, and when hovering mods"/>

        <flex>
            <a-select v-model="mod.game_id" label="Game" placeholder="Select a game" :options="store.games?.data"/>
            <a-select v-model="mod.category_id" label="Category" placeholder="Select a category" :disabled="!mod.game_id" :options="mod.game_id && categories?.data"/>
        </flex>

        <a-select v-model="mod.tag_ids" placeholder="Select tags" :options="tags.data" multiple label="Tags" desc="Make your mod more discoverable"/>

        <a-select v-model="mod.visibility" label="Visiblity" placeholder="Select a category" :options="visItems"/>

        <details>
            <summary>{{$t('license')}}</summary>
            <flex class="mt-3" column gap="2">
                <small><a href="https://choosealicense.com/" target="_blank">Can't choose?</a></small>
                <md-editor v-model="mod.license" rows="6"/>
            </flex>
        </details>

        <a-alert class="w-full" color="danger" title="DANGER ZONE">
            <div>
                <a-button color="danger" @click="deleteMod">Delete</a-button>
            </div>
        </a-alert>
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod } from '~~/types/models.js';
const { init: openModal } = useModal();

const router = useRouter();

const props = defineProps<{
    mod: Mod
}>();

const visItems = [
    { name: 'Public', value: 1 },
    { name: 'Hidden', value: 2 },
    { name: 'Unlisted', value: 3 }
];

function deleteMod() {
    openModal({
        message: 'Are you sure you want to delete the mod?',
        async onOk() {
            await useDelete(`mods/${props.mod.id}`);
            router.push('/');
        }
    });
}

const store = useStore();
const { data: categories, refresh: refetchCats } = await useAsyncData('fetch-categories', () => useGet(`games/${props.mod.game_id}/categories`));
const { data: tags } = await useAsyncData('getTagsAndGames', () => useGet('/tags'));
await store.fetchGames();

watch(() => props.mod.game_id, val => {
    if (val) {
        refetchCats();
    }
});
</script>