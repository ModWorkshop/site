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
                <group class="mx-auto mt-4" style="width: unset">
                    <el-input type="submit" value="Save"/>
                </group>
            </el-form>
        </div>
    </flex>
</template>

<script setup>
    import { useContext, useFetch } from '@nuxtjs/composition-api';
    import editModMain from '../components/pages/edit-mod/main.vue';
    let isNew = $ref(true);
    const { $factory, params } = useContext();
    let mod = $ref({
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
        visibility: 1
    });

    useFetch(async () => {
        if (params.value.id) {
            isNew = false;
            mod = await $factory.getOne('mods', params.value.id);
        }
    });

    async function save() {
        try {
            if (isNew) {
                mod = await $factory.create('mods', mod);
            } else {
                await $factory.update('mods', mod.id, mod);
            }
        } catch (error) {
            console.error(error);
            return;
        }
    }
</script>