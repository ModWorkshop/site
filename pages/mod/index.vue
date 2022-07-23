<template>
    <page-block>
        <Head>
            <Title>{{mod.name}}</Title>
        </Head>
        <breadcrumbs :items="mod.breadcrumb"/>
        <flex>
            <!-- TODO: make our own buttons -->
            <nuxt-link v-if="canEdit" :to="`/mod/${mod.id}/edit`">
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
                                <strong>{{$t('last_updated')}}</strong> <span :title="mod.bump_date">{{updateDateAgo}}</span>
                            </span>
                        </div>
                        <flex class="md:ml-auto">
                            <a v-if="canLike" id="like-button" class="btn btn-lg d-flex align-items-center flex-grow px-2 py-2 btn-danger">
                                <i v-if="mod.liked" class="ri-heart-fill mx-1"/>
                                <i v-else class="ri-heart-line mx-1"/>
                            </a>
                            <a-button v-if="mod.download" class="download-button w-full text-center" icon="download" :href="`http://localhost:8000/files/${mod.download.id}/download`" download>
                                Download
                                <br>
                                <span class="text-sm">{{mod.download.type}} - {{friendlySize(mod.download.size)}}</span>
                            </a-button>
                            <a-button v-else-if="mod.files && mod.files.length > 0" href="#downloads" class="download-button" icon="download">Downloads</a-button>
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
        <the-comments :url="`mods/${mod.id}/comments`" :can-edit-all="canEdit" :get-special-tag="commentSpecialTag"/>
    </page-block>
</template>

<script setup lang="ts">
import { friendlySize, timeAgo } from '~~/utils/helpers';
import { useStore } from '~~/store';
import { Mod, Comment } from '~~/types/models';
import { useI18n } from 'vue-i18n';

const store = useStore();
const route = useRoute();

const { t } = useI18n();

const { data: mod } = await useFetchData<Mod>(`mods/${route.params.id}/`);

const publishDateAgo = computed(() => timeAgo(mod.value.publish_date));
const updateDateAgo = computed(() => timeAgo(mod.value.bump_date));
const modStatus = computed(() => '');
//Guests can't actually like the mod, it's just a redirect.
const canLike = computed(() => !store.user || store.user.id !== mod.value.submitter_id);
const canEdit = computed(() => (store.user && mod.value.submitter_id == store.user.id) || store.hasPermission('admin'));

function commentSpecialTag(comment: Comment) {
    if (comment.user_id === mod.value.submitter_id) {
        return `(${t('author')})`;
    } //TODO: Collaborators
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