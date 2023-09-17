<template>
    <a-banner class="mod-banner" :src="mod.legacy_banner_url || (mod.banner && mod.banner.file)" height="250" url-prefix="mods/images">
        <flex column class="mod-data">
            <flex class="mod-data-top">
                <span class="mod-title">{{mod.name}}</span>
                <mod-status class="ml-auto text-xl" :mod="mod"/>
            </flex>
            <flex column class="mt-auto md:flex-row">
                <flex gap="3" class="items-center mt-auto">
                    <span v-if="mod.version" :title="$t('version')">
                        <i-mdi-tag/> {{mod.version}}
                    </span>
                    <span v-if="mod.bumped_at">
                        <span :title="$t('last_updated')" class="mr-1">
                            <i-mdi-clock/>
                        </span>
                        <time-ago v-if="!mod.last_user" :time="mod.bumped_at"/>
                        <span v-else class="items-center inline-flex gap-1">
                            <i18n-t keypath="by_user_time_ago" scope="global">
                                <template #user>
                                    <a-user avatar-size="xs" :user="mod.last_user" :tag="false"/>
                                </template>
                                <template #time>
                                    <time-ago :time="mod.bumped_at"/>
                                </template>
                            </i18n-t>
                        </span>
                    </span>
                </flex>
                <flex class="md:ml-auto max-md:content-stretch" style="box-shadow: initial; text-shadow: initial;">
                    <a-button v-if="canLike" :color="mod.liked && 'danger' || 'secondary'" class="large-button" :to="!user ? '/login' : undefined" @click="toggleLiked">
                        <i-mdi-heart/>
                    </a-button>

                    <a-button v-if="download && downloadType == 'file'" class="large-button flex-1" :to="!static ? downloadUrl : undefined">
                        <i-mdi-download/> {{$t('download')}}
                        <br>
                        <span class="text-sm">{{(download as any).type}} - {{friendlySize((download as any).size)}}</span>
                    </a-button>
                    <VDropdown v-else-if="download && downloadType == 'link'">
                        <a-button class="large-button flex-1" @click="!static && registerDownload(mod)">
                            <i-mdi-download/> {{$t('show_download_link')}}
                        </a-button>
                        <template #popper>
                            <div class="word-break p-2" style="width: 250px;">
                                {{$t('show_download_link_warn')}}
                                <br>
                                <a class="text-lg font-bold" :href="(download as any).url">{{(download as any).url}}</a>
                            </div>
                        </template>
                    </VDropdown>
                    <a-button v-else-if="(mod.files_count || (mod.files && mod.files.data.length)) || (mod.links && mod.links.data.length)" class="large-button flex-1" @click="switchToFiles">{{$t('downloads')}}</a-button>
                    <a-button v-else class="large-button flex-1" disabled><i-mdi-download/> {{$t('no_downloads')}}</a-button>
                </flex>
            </flex>
        </flex>
    </a-banner>
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
import { friendlySize, setQuery } from '~~/utils/helpers';
import { registerDownload } from '~~/utils/mod-helpers';

const props = defineProps<{
    mod: Mod,
    static?: boolean
}>();

const { user, hasPermission } = useStore();

//Guests can't actually like the mod, it's just a redirect.
const canLike = computed(() => !user || (user.id !== props.mod.user_id && hasPermission('like-mods', props.mod.game)));

const download = computed(() => props.mod.download);
const downloadType = computed(() => {
    if (props.mod.download_type) {
        return props.mod.download_type;
    } else if (download.value) {
        return Object.hasOwn(download.value, 'file') ? 'file' : 'link';
    }
});

const downloadUrl = computed(() => `/mod/${props.mod.id}/download/${download.value!.id}`);

async function toggleLiked() {
    if (props.static || !user) {
        return;
    }

    const data = await postRequest<{ liked: boolean, likes: number }>(`mods/${props.mod.id}/toggle-liked`);
    props.mod.likes = data.likes;
    props.mod.liked = data.liked;
}

function switchToFiles() {
    setQuery('tab', 'downloads');
}
</script>
<style>
.large-button {
    font-size: 1.25rem;
    padding: 0.5rem 1.5rem !important;
    text-align: center;
}
</style>
<style scoped>
.mod-banner {
    box-shadow: inset 0px 0px 30px 20px rgba(0,0,0, 0.45);
    text-shadow: 2px 1px 3px black;
}

.mod-data {
    height: 100%;
    padding: 0.75rem;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0));
}

.light .mod-banner {
    box-shadow: inset 0px 0px 30px 20px rgba(223, 223, 223, 0.2);
    text-shadow: 2px 1px 3px rgb(88, 88, 88);
}

.light .mod-data {
    background: linear-gradient(to right, rgba(128, 128, 128, 0.45), rgba(128, 128, 128, 0));
}

.mod-data-top {
    overflow: hidden;
    height: 148px;
    word-break: break-word;
}

.mod-title {
    font-size: 2rem;
}

.mod-banner .data .version {
    font-weight: normal;
}
</style>