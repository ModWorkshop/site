<template>
    <flex gap="3" column class="content-block-large">
        <flex>
            <!-- TODO: make our own buttons -->
            <nuxt-link v-if="mod.id" :to="`/mod/${mod.id}`">
                <a-button icon="arrow-left">{{$t('return_to_mod')}}</a-button>
            </nuxt-link> 
        </flex>
        <div class="content-block">
            <el-form class="p-4 px-16" @submit.native.prevent="save" style="display: flex; flex-direction: column;">
                <tabs padding="4" tab-position="left" list>
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
            </el-form>
        </div>
        <!-- Dunno why SSR really dislikes me doing the deep check, anyway this shouldn't be relevant for SSR. Though check in Nuxt3 if it's fixed. -->
        <client-only>
            <transition name="fade">
                <div v-if="shouldSave" class="fixed p-2" style="right: 32px; bottom: 32px; background-color: #00000040; border-radius: 3px;">
                    <small>{{saveText}}</small>
                    <a-button @click="save">{{saveButtonText}}</a-button>
                </div>
            </transition>
        </client-only>
    </flex>
</template>

<script>
    import clone from 'rfdc/default';
    import { deepEqual } from 'fast-equals';

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
            modCopy: clone(modTemplate),
            isNew: true,
        }),
        computed: {
            shouldSave() {
                return this.isNew || !deepEqual(this.mod, this.modCopy);
            },
            saveText() {
                return this.isNew ? 'Your mod is not uploaded yet' : 'You have unsaved changes';
            },
            saveButtonText() {
                return this.isNew ? this.$t('upload') : this.$t('save');
            }
        },
        methods: {
            async save() {
                try {
                    if (this.isNew) {
                        this.mod = await this.$factory.create('mods', this.mod);
                        this.isNew = false;
                    } else {
                        await this.$factory.update('mods', this.mod.id, this.mod);
                    }
                    this.modCopy = clone(this.mod);
                } catch (error) {
                    console.error(error);
                    return;
                }
            }
        },
        async asyncData({ $factory, params }) {
            if (params.id) {
                const mod = await $factory.getOne('mods', params.id);

                mod.tag_ids = mod.tags.map(tag => tag.id);

                const modCopy = clone(mod);
    
                return { mod, modCopy, isNew: false };
            } else {
                return { mod: clone(modTemplate) };
            }
        }
    };
</script>

<style>
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.25s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
</style>