<template>
    <page-block size="med">
        <flex>
            <nuxt-link v-if="mod.id" :to="`/mod/${mod.id}`">
                <a-button icon="arrow-left">{{$t('return_to_mod')}}</a-button>
            </nuxt-link> 
        </flex>
        <a-form :model="mod" :created="mod.id == -1" :save-text="saveText" float-save-gui @submit="save">
            <content-block class="p-8">
                <a-tabs padding="4" side>
                    <a-tab name="main" title="Main">
                        <edit-mod-main :mod="mod"/>
                    </a-tab>
                    <a-tab name="files" title="Files & Updates">
                        <edit-mod-files :mod="mod"/>
                    </a-tab>
                    <a-tab name="images" title="Images">
                        <edit-mod-images :mod="mod"/>
                    </a-tab>
                    <a-tab name="contributors" title="Contributors"/>
                    <a-tab name="instructions" title="Instructions"/>
                </a-tabs>
            </content-block>
        </a-form>
    </page-block>
</template>

<script setup lang="ts">
    import clone from 'rfdc/default';
    import { Mod } from '~~/types/models';
    const route = useRoute();

    const modTemplate = {
        id: -1,
        name: '',
        desc: '',
        images: [],
        files: [],
        short_desc: '',
        game_id: null,
        category_id: null,
        tag_ids: [],
        version: '',
        nsfwMod: false,
        download_id: null,
        download_type: null,
        visibility: 1
    };

    // const store = useStore();
    const mod = ref<Mod>(clone(modTemplate));

    if (route.params.id) {
        const { data: fetchedMod } = await useFetchData<Mod>(`mods/${route.params.id}/`);
        mod.value = fetchedMod.value;
        // mod.tag_ids = mod.tags.map(tag => tag.id);
    }

    const saveText = computed(() => mod.value.id == -1 ? 'Your mod is not uploaded yet' : 'You have unsaved changes');
    async function save() {
        try {
            if (mod.value.id == -1) {
                mod.value = await usePost<Mod>('mods', mod.value);
                history.replaceState(null, null, `/mod/${mod.value.id}/edit`);
            } else {
                mod.value = await usePatch<Mod>(`mods/${mod.value.id}`, mod.value);
            }
        } catch (error) {
            console.error(error);
            return;
        }
    }
</script>