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
            <a-banner class="mod-banner" :src="mod.legacy_banner_url || (mod.banner && mod.banner.file)" height="250" url-prefix="mods/images">
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
                            <a-button v-if="canLike" id="like-button" :color="mod.liked && 'danger' || 'secondary'" class="large-button" icon="heart" @click="toggleLiked"/>
                            <a-button v-if="mod.download" class="large-button w-full text-center" icon="download" :href="`http://localhost:8000/files/${mod.download.id}/download`" download @click="registerDownload">
                                Download
                                <br>
                                <span class="text-sm">{{mod.download.type}} - {{friendlySize(mod.download.size)}}</span>
                            </a-button>
                            <a-button v-else-if="mod.files && mod.files.length > 0" href="#downloads" class="download-button" icon="download">Downloads</a-button>
                            <a-button v-else class="download-button" disabled>No Files</a-button>
                        </flex>
                    </flex>
                </flex>
            </a-banner>
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

if (mod.value) {
    usePost(`mods/${mod.value.id}/register-view`, null, {
        async onResponse({ response }) {
            if (response.status == 201) {
                mod.value.views++;
            }
        }
    });
}

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

function registerDownload() {
    usePost(`mods/${mod.value.id}/register-download`, null, {
        async onResponse({ response }) {
            if (response.status == 201) {
                mod.value.downloads++;
            }
        }
    });
}

async function toggleLiked() {
    const data = await usePost<{ liked: boolean, likes: number }>(`mods/${mod.value.id}/toggle-liked`);
    mod.value.likes = data.likes;
    mod.value.liked = data.liked;
}
</script>

<style>
    .mod-banner {
        box-shadow: 1px 1px 5px #000;
        box-shadow: inset 0px 0px 30px 20px rgba(0,0,0, 0.45);
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

    .large-button {
        font-size: 1.5rem;
        padding: 0.5rem 1.5rem !important;
    }


    .mod-banner a > span {
        text-shadow: 1px 1px rgba(0, 0, 0, 0.3);
    }
</style>