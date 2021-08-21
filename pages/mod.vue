<template>
    <div style="width: 87%;">
        <flex style="border-radius:.25rem">
            <div class="mod-banner flex-grow" :style="`background:${mod.banner || 'https://modworkshop.net/images/default_banner.png'};`">
                <flex column class="flex-grow p-3 data">
                    <div style="font-weight: normal;overflow: hidden;height: 148px;word-break: break-word;">
                        <span id="title">{{mod.name}}</span>
                        <br>
                        <span style="font-weight: normal; font-size: 0.95em;">
                            <strong>{{$t('submitted_by')}}</strong>
                            <user :user="mod.submitter"/>
                            <span v-if="mod.publish_date" :title="mod.publish_date">{{pubDateRelative}}</span>
                        </span>
                    </div>
                    <flex column class="mt-auto flex-md-row">
                        <div class="p-0 version mt-auto">
                            <span style="font-size: 0.95em;">
                                <strong v-if="modStatus">{{modStatus}}</strong>
                                <strong v-if="mod.version && mod.version|length <= 24">{{$t('version')}} {{mod.version}}</strong>
                                <strong>{{$t('last_updated')}}</strong> <!--TODO: implement last updater-->
                                <span :title="mod.bump_date">{{mod.bumpDateRelative}}</span>
                            </span>
                        </div>
                        <flex class="ml-md-auto mt-2 mt-md-0">
                            <a v-if="canLike" id="like-button" class="btn btn-lg d-flex align-items-center flex-grow px-2 py-2 btn-danger">
                                <i v-if="mod.liked" class="ri-heart-fill mx-1"/>
                                <i v-else class="ri-heart-line mx-1"/>
                            </a>
                            <!--{% include "parts/modpage/download_button.twig" %}-->
                        </flex>
                    </flex>
                </flex>
                 Tabs
                <tabs>
                    <tab name="a" title="Tab A">
                        xddd
                    </tab>
                    <tab name="b" title="Tab B">
                        ok
                    </tab>
                    <tab name="c" title="Tab C">
                        yo
                    </tab>
                </tabs>
            </div>
        </flex>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    //TODO: implement pipe split for mod status and whatnot
    export default {
        data() {
            return {
                mod: {}
            }
        },
        computed: {
            pubDateRelative() {
                return 'A few pogs ago';
            },
            bumpDateRelative() {
                return 'A few pogs ago';
            },
            modStatus() {
                return null;
            },
            canLike() {
                //Guests can't actually like the mod, it's just a redirect.
                return !this.user || this.user.id !== this.mod.submitter_uid;
            },
            ...mapGetters([
                'user'
            ])
        },
        async asyncData({params, $factory}) {
            if (params.id) {
                return { mod: await $factory.getOne('mods', params.id) };
            }
        }
    }
</script>