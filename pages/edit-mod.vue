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
                <el-tabs tab-position="left">
                    <el-tab-pane label="Main">
                        <edit-mod-main :mod="mod"/>
                    </el-tab-pane>
                    <el-tab-pane label="Files & Images">

                    </el-tab-pane>
                    <el-tab-pane label="Extra">

                    </el-tab-pane>
                </el-tabs>
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