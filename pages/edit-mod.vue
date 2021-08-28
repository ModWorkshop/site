<template>
    <flex gap="3" column class="content-block-large">
        <flex>
            <!-- TODO: make our own buttons -->
            <nuxt-link :to="`/mod/${this.mod.id}`">
                <a-button icon="arrow-left">{{$t('return_to_mod')}}</a-button>
            </nuxt-link> 
        </flex>
        <div class="content-block">
            <el-form @submit.native.prevent="save" style="display: flex; flex-direction: column;">
                <tabs tab-position="left" list>
                    <tab name="main" title="Main">
                        <edit-mod-main :mod="mod"/>
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
<script>
export default {
    data() {
        return {
            newMod: true,
            mod: {
                name: '',
                desc: '',
                root: 1,
                cid: 1,
                tags: [],
                version: '',
                nsfwMod: false,
                visibility: 1
            }
        }
    },
    methods: {
        async save() {
            try {
                if (this.newMod) {
                    await this.$factory.create('mods', this.mod);
                } else {
                    await this.$factory.update('mods', this.mod.id, this.mod);
                }
            } catch (error) {
                console.error(error);
                return;
            }
        },
    },
    async asyncData({params, $factory}) {
        if (params.id) {
            return { mod: await $factory.getOne('mods', params.id), newMod: false };
        }
    }
}
</script>