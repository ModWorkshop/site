<template>
    <page-block>
        <breadcrumbs :items="mod.breadcrumb"/>
        <flex>
            <!-- TODO: make our own buttons -->
            <nuxt-link v-if="canEdit" :to="`/mod/${this.mod.id}/edit`">
                <a-button icon="cog">{{$t('edit_mod')}}</a-button>
            </nuxt-link>
        </flex>
        <flex style="border-radius: 0.25rem">
            <div class="mod-banner flex-grow" :style="`background:url('${mod.banner || 'https://modworkshop.net/images/default_banner.png'}');`">
                <flex column class="flex-grow p-3 data">
                    <div style="font-weight: normal;overflow: hidden;height: 148px;word-break: break-word;">
                        <span id="title">{{mod.name}}</span>
                        <br>
                        <span v-if="mod.publish_date">
                            <strong>{{$t('submitted')}}</strong>
                            <!-- <a-user :user="mod.submitter" avatar-size="small"/> -->
                            <span :title="mod.publish_date">{{publishDateAgo}}</span>
                        </span>
                    </div>
                    <flex column class="mt-auto md:flex-row">
                        <div class="p-0 version mt-auto">
                            <span>
                                <strong v-if="modStatus">{{modStatus}}</strong>
                                <strong v-if="mod.version && mod.version.length <= 24">{{$t('version')}} {{mod.version}}</strong>
                                <strong>{{$t('last_updated')}}</strong> <!--TODO: implement last updater-->
                                <span :title="mod.updated_at">{{updateDateAgo}}</span>
                            </span>
                        </div>
                        <flex class="md:ml-auto">
                            <a v-if="canLike" id="like-button" class="btn btn-lg d-flex align-items-center flex-grow px-2 py-2 btn-danger">
                                <i v-if="mod.liked" class="ri-heart-fill mx-1"/>
                                <i v-else class="ri-heart-line mx-1"/>
                            </a>
                            <form v-if="mod.download" :action="`http://localhost:8000/files/${mod.download.id}/download`" method="get" class="flex-grow ml-2">
                                <a-button class="download-button w-full" icon="download">
                                    Download
                                    <br>
                                    <span class="text-sm">{{mod.download.type}} - {{friendlySize(mod.download.size)}}</span>
                                </a-button>
                            </form>
                            <a-button v-else-if="mod.files.length > 0" href="#downloads" class="download-button" icon="download">Downloads</a-button>
                            <a-button v-else class="download-button" disabled>No Files</a-button>
                        </flex>
                    </flex>
                </flex>
            </div>
        </flex>
        <div class="mod-main">
            <mod-tabs :mod="mod"/>
            <mod-right-pane :mod="mod"/>
        </div>
        <comments :url="`mods/${mod.id}/comments`" :can-edit-all="canEdit"/>
    </page-block>
</template>

<script>
    import { mapState, mapStores } from 'pinia';
    import { timeAgo, friendlySize } from '../utils/helpers';
    import { parseMarkdown } from '../utils/md-parser';
    import { useStore } from '../store';

    //TODO: implement pipe split for mod status and whatnot
    export default {
        data: () => ({
            mod: {}
        }),
        methods: {
            parseMarkdown,
            friendlySize
        },
        computed: {
            canEdit() {
                return this.mod.submitter_id === this.user.id || this.mainStore.hasPermission('admin');
            },
            publishDateAgo() {
                return timeAgo(this.mod.publish_date);
            },
            updateDateAgo() {
                return timeAgo(this.mod.updated_at);
            },
            modStatus() {
                return null;
            },
            canLike() {
                //Guests can't actually like the mod, it's just a redirect.
                return !this.user || this.user.id !== this.mod.submitter_id;
            },
            ...mapStores(useStore),
            ...mapState(useStore, [
                'user'
            ])
        },
        async asyncData({params, $factory}) {
            if (params.id) {
                return { mod: await $factory.getOne('mods', params.id) };
            }
        }
    };
</script>

<style>
    .mod-banner {
        background-size: cover;
        box-shadow: 1px 1px 5px #000;
        box-shadow: inset 0px 0px 30px 20px rgba(0,0,0, 0.45);
        height: 250px;
        border-radius: .25rem;
    }

    .mod-banner .data {
        height: 100%;
        font-weight: bold;
        color: var(--MAIN_COLOR_TEXT);
        background: linear-gradient(to right, rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0));
    }

    .mod-banner #title {
        font-size: 20pt;
    }

    .mod-banner .data .version {
        font-weight: normal;
    }

    .mod-main {
        display: grid;
        grid-gap: .75rem;
        margin-right: .75rem;
        grid-template-columns: 70% 30%
    }

    .desc-content img {
        max-width: 100%;
    }

    .fixed-anchor {
        position: relative;
        top: -64px;
    }

    @media (min-width:600px) and (max-width:850px) {
        .mod-info .thumbnail {
            display: none;
        }
    }

    @media (max-width:850px) {
        .mod-banner {
            height: 295px;
        }

        .mod-info {
            order: -1;
        }

        .mod-main {
            grid-template-columns: auto;
            margin-right: 0;
        }
        .contributor-block .info{
            line-height: 32px;
        }
        .contributor-block .avatar {
            height: 64px;
            width: 64px;
        }
    }

    a.like-button {
        border-bottom: 2px solid var(--primary);
    }

    a.like-button.unliked {
        border-color: var(--secondary);
    }

    a.like-button:hover {
        border-color: var(--a-hover);
    }
    a.like-button.unliked:hover {
        border-color: var(--primary);
    }

    .download-button {
        font-size: 1.25rem;
        padding: 0.5rem 2rem !important;
    }

    .unliked {
        color: var(--white);
    }

    .mod-banner a > span {
        text-shadow: 1px 1px rgba(0, 0, 0, 0.3);
    }
</style>