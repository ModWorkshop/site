<template>
    <page-block size="med">
        <flex>
            <nuxt-link v-if="mod.id" :to="`/mod/${mod.id}`">
                <a-button icon="arrow-left">{{$t('return_to_mod')}}</a-button>
            </nuxt-link> 
        </flex>
        <a-form @submit="save" :model="mod" :rules="rules"
                :created="!isNew" :save-text="saveText" 
                :can-save="this.isNew" float-save-gui>
            <content-block class="p-8">
                <tabs padding="4" side>
                    <tab name="main" title="Main">
                        <edit-mod-main :modData="mod"/>
                    </tab>
                    <tab name="files" title="Files & Updates">
                        <edit-mod-files :modData="mod"/>
                    </tab>
                    <tab name="images" title="Images">
                        <edit-mod-images :modData="mod"/>
                    </tab>
                    <tab name="contributors" title="Contributors">

                    </tab>
                    <tab name="instructions" title="Instructions">

                    </tab>
                </tabs>
            </content-block>
        </a-form>
    </page-block>
</template>

<script>
    import clone from 'rfdc/default';
    import { useStore } from '../store';

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

    export default {
        data: () => ({
            mod: clone(modTemplate),
            isNew: true,
            rules: {
                name: {min: 3}
            }
        }),
        computed: {
            saveText() {
                return this.isNew ? 'Your mod is not uploaded yet' : 'You have unsaved changes';
            },
        },
        methods: {
            async save() {
                try {
                    if (this.isNew) {
                        this.mod = await this.$factory.create('mods', this.mod);
                        this.isNew = false;
                    } else {
                        this.mod = await this.$factory.update('mods', this.mod.id, this.mod);
                    }
                } catch (error) {
                    console.error(error);
                    return;
                }
            }
        },
        async asyncData({ $factory, $pinia, params, error }) {
            const store = useStore($pinia);
            if (params.id) {
                const mod = await $factory.getOne('mods', params.id);

                mod.tag_ids = mod.tags.map(tag => tag.id);

                const modCopy = clone(mod);

                if (mod.submitter_id !== store.user.id && !store.hasPermission('admin')) {
                    error({
                        statusCode: 401,
                        message: "You don't have the right permissions to edit this mod!"
                    });
                }
    
                return { mod, modCopy, isNew: false };
            } else {
                return { mod: clone(modTemplate) };
            }
        }
    };
</script>