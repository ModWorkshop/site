<template>
    <page-block size="med">
        <flex>
            <nuxt-link v-if="mod.id" :to="`/mod/${mod.id}`">
                <a-button icon="arrow-left">{{$t('return_to_mod')}}</a-button>
            </nuxt-link> 
        </flex>
        <a-form @submit="save" :model="mod" :rules="rules"
                :created="!isNew" :save-text="saveText" 
                :can-save="isNew" float-save-gui>
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
                    <a-tab name="contributors" title="Contributors">

                    </a-tab>
                    <a-tab name="instructions" title="Instructions">

                    </a-tab>
                </a-tabs>
            </content-block>
        </a-form>
    </page-block>
</template>

<script setup>
    import clone from 'rfdc/default';
    const route = useRoute();

    let modTemplate = {
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
    const mod = ref(clone(modTemplate));
    const isNew = ref(!route.params.id);

    if (route.params.id) {
        const { data: fetchedMod } = await useAPIFetch(`mods/${route.params.id}/`);
        mod.value = fetchedMod.value;
        // mod.tag_ids = mod.tags.map(tag => tag.id);
    }

    const rules = {
        name: {min: 3}
    };
    const saveText = computed(() => isNew.value ? 'Your mod is not uploaded yet' : 'You have unsaved changes');

    async function save() {
        try {
            if (isNew.value) {
                mod.value = await this.$ftch.post('mods', mod.value);
                isNew.value = false;
            } else {
                mod.value = await this.$ftch.patch(`mods/${mod.value.id}`, mod.value);
            }
        } catch (error) {
            console.error(error);
            return;
        }
    }
</script>