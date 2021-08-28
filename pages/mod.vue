<template>
    <flex column gap="3" class="content-block-large">
        <flex>
            <!-- TODO: make our own buttons -->
            <nuxt-link :to="`/mod/${this.mod.id}/edit`">
                <el-button type="primary"><font-awesome-icon icon="cog"/> {{$t('edit_mod')}}</el-button>
            </nuxt-link>
        </flex>
        <flex style="border-radius: 0.25rem">
            <div class="mod-banner flex-grow" :style="`background:url('${mod.banner || 'https://modworkshop.net/images/default_banner.png'}');`">
                <flex column class="flex-grow p-3 data">
                    <div style="font-weight: normal;overflow: hidden;height: 148px;word-break: break-word;">
                        <span id="title">{{mod.name}}</span>
                        <br>
                        <span>
                            <strong>{{$t('submitted_by')}}</strong>
                            <user :user="mod.submitter" avatar-size="small"/>
                            <span v-if="mod.publish_date" :title="mod.publish_date">{{publishDateAgo}}</span>
                        </span>
                    </div>
                    <flex column class="mt-auto flex-md-row">
                        <div class="p-0 version mt-auto">
                            <span>
                                <strong v-if="modStatus">{{modStatus}}</strong>
                                <strong v-if="mod.version && mod.version|length <= 24">{{$t('version')}} {{mod.version}}</strong>
                                <strong>{{$t('last_updated')}}</strong> <!--TODO: implement last updater-->
                                <span :title="mod.updated_at">{{updateDateAgo}}</span>
                            </span>
                        </div>
                        <flex class="ml-md-auto">
                            <a v-if="canLike" id="like-button" class="btn btn-lg d-flex align-items-center flex-grow px-2 py-2 btn-danger">
                                <i v-if="mod.liked" class="ri-heart-fill mx-1"/>
                                <i v-else class="ri-heart-line mx-1"/>
                            </a>
                            <!--{% include "parts/modpage/download_button.twig" %}-->
                        </flex>
                    </flex>
                </flex>
            </div>
        </flex>
        <div class="mod-main">
            <flex grow>
                <tabs class="content-block flex-grow">
                    <tab name="description" :title="$t('description')" v-html="mdDesc"/>
                    <tab name="images" :title="$t('images')">Nothing for now!</tab>
                    <tab name="files" :title="$t('files')">Nothing for now!</tab>
                    <tab v-if="mod.changelog" name="changelog" :title="$t('changelog')">{{mod.changelog}}</tab>
                    <tab v-if="mod.license" name="license" :title="$t('license')">{{mod.license}}</tab>
                    <tab name="instructions" :title="$t('instructions')">Nothing for now!</tab>
                </tabs>
            </flex>
            <flex column gap="1" class="mod-info content-block p-2">
                <div class="thumbnail overflow-hidden ratio-image-mod-thumb">
                    <mod-thumbnail :mod="mod"/>
                </div>
                <div class="p-2" style="font-size: 20px">
                    <div class="p-1 inline-block">
                        <font-awesome-icon icon="heart"/>
                        <span id="likes">{{likes}}</span>
                    </div>
                    <div class="p-1 inline-block">
                        <font-awesome-icon icon="download"/>
                        <span>{{downloads}}</span>
                    </div>
                    <div class="p-1 inline-block">
                        <font-awesome-icon icon="eye"/>
                        <span>{{views}}</span>
                    </div>
                </div>
                <div class="p-2 tags-block">
                    <!-- TODO: Don't forget to make them link -->
                    <el-tag effect="dark">Temp</el-tag>
                    <el-tag effect="dark">Anime</el-tag>
                    <el-tag effect="dark">Pog</el-tag>
                </div>
                <div class="p-2 colllaborators-block">
                    <user avatarSize="medium" :user="mod.submitter" :details="$t('submitter')"/>
                </div>
            </flex>
        </div>
    </flex>
</template>

<script>
    import { mapGetters } from 'vuex';
    import { DateTime } from 'luxon';
    import { parseMarkdown } from '../utils/md-parser';

    //TODO: implement pipe split for mod status and whatnot
    export default {
        data() {
            return {
                mod: {}
            }
        },
        computed: {
            mdDesc() {
                return parseMarkdown(this.mod.desc);
            },
            likes() {
                return 0;
            },
            downloads() {
                return 0;
            },
            views() {
                return 1;
            },
            publishDateAgo() {
                return DateTime.fromISO(this.mod.publish_date).toRelative();
            },
            updateDateAgo() {
                return DateTime.fromISO(this.mod.updated_at).toRelative();
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

.unliked {
    color: var(--white);
}

.mod-banner a > span {
    text-shadow: 1px 1px rgba(0, 0, 0, 0.3);
}

.reply {
    background-color: #151719;
}

.comment-body .comment-actions {
    visibility: hidden
}

.comment-body:hover .comment-actions {
    visibility: visible
}
</style>