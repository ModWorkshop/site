<template>
    <flex gap="3" column class="content-block-large">
        <flex>
            <!-- TODO: make our own buttons -->
            <nuxt-link v-if="mod.id" :to="`/mod/${mod.id}`">
                <a-button icon="arrow-left">{{$t('return_to_mod')}}</a-button>
            </nuxt-link> 
        </flex>
        <div class="content-block">
            <el-form @submit.native.prevent="save" style="display: flex; flex-direction: column;">
                <tabs tab-position="left" list>
                    <tab name="main" title="Main">
                        <edit-mod-main :modData="mod"/>
                    </tab>
                    <tab name="files" title="Files & Images">

                    </tab>
                    <tab name="extra" title="Extra">

                    </tab>
                </tabs>
                <el-form-item class="mx-auto" style="width: unset">
                    <el-input type="submit" value="Save"/>
                </el-form-item>
            </el-form>
        </div>
    </flex>
</template>

<script setup>
    import { useContext, useFetch } from '@nuxtjs/composition-api';
    let isNew = $ref(true);
    const { $factory, params } = useContext();
    let mod = $ref({
        name: '',
        desc: '',
        game_id: null,
        category_id: null,
        tags: [],
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