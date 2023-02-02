<template>
    <a-banner class="mod-banner" :src="mod.legacy_banner_url || (mod.banner && mod.banner.file)" height="250" url-prefix="mods/images">
        <flex column class="mod-data">
            <flex class="mod-data-top">
                <span class="mod-title">{{mod.name}}</span>
                <mod-status class="ml-auto" :mod="mod"/>
            </flex>
            <flex column class="mt-auto md:flex-row">
                <flex gap="3" class="items-center mt-auto">
                    <span v-if="mod.version">
                        <a-icon icon="tag" :title="$t('version')"/> {{mod.version}}
                    </span>
                    <span v-if="mod.bumped_at">
                        <a-icon icon="clock" :title="$t('last_updated')" class="mr-1"/>
                        <time-ago v-if="!mod.last_user" :time="mod.bumped_at"/>
                        <span v-else class="items-center inline-flex gap-1">
                            <i18n-t keypath="by_user_time_ago">
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
                <flex class="md:ml-auto">
                    <a-button v-if="canLike" :color="mod.liked && 'danger' || 'secondary'" class="large-button" icon="heart" :to="!user ? '/login' : null" @click="toggleLiked"/>
                    <a-button v-if="mod.download && mod.download_type == 'file'" class="large-button text-center" icon="download" :to="!static ? downloadUrl : null">
                        {{$t('download')}}
                        <br>
                        <span class="text-sm">{{(mod.download as any).type}} - {{friendlySize((mod.download as any).size)}}</span>
                    </a-button>
                    <VDropdown v-else-if="mod.download && mod.download_type == 'link'">
                        <a-button class="large-button w-full text-center" icon="download" @click="!!static && registerDownload">
                            {{$t('show_download_link')}}
                        </a-button>
                        <template #popper>
                            <div class="word-break p-2" style="width: 250px;">
                                {{$t('show_download_link_warn')}}
                                <br>
                                <a :href="(mod.download as any).url">{{(mod.download as any).url}}</a>
                            </div>
                        </template>
                    </VDropdown>
                    <a-button v-else-if="mod.files.length || mod.links.length" class="large-button" icon="download" @click="switchToFiles">{{$t('downloads')}}</a-button>
                    <a-button v-else class="large-button" disabled>{{$t('no_downloads')}}</a-button>
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
const downloadUrl = computed(() => `/mod/${props.mod.id}/download/${props.mod.download.id}`);

async function toggleLiked() {
    if (props.static) {
        return;
    }

    const data = await usePost<{ liked: boolean, likes: number }>(`mods/${props.mod.id}/toggle-liked`);
    props.mod.likes = data.likes;
    props.mod.liked = data.liked;
}

function switchToFiles() {
    setQuery('tab', 'downloads');
}
</script>
<style>
.large-button {
    font-size: 1.5rem;
    padding: 0.5rem 1.5rem !important;
    box-shadow: initial;
    text-shadow: initial;
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
    color: white;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0));
}

.light .mod-banner {
    box-shadow: inset 0px 0px 30px 20px rgba(0,0,0, 0.2);
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