<template>
    <a-banner class="mod-banner" :src="mod.legacy_banner_url || (mod.banner && mod.banner.file)" height="250" url-prefix="mods/images">
        <flex column class="mod-data">
            <flex class="mod-data-top">
                <span id="title">{{mod.name}}</span>
                <mod-status class="ml-auto" :mod="mod"/>
            </flex>
            <flex column class="mt-auto md:flex-row">
                <div class="mt-auto">
                    <flex gap="3" class="items-center">
                        <span v-if="mod.version">
                            <font-awesome-icon icon="tag" :title="$t('version')"/> {{mod.version}}
                        </span>
                        <span v-if="mod.bumped_at">
                            <font-awesome-icon icon="clock" :title="$t('last_updated')" class="mr-1"/>
                            <time-ago v-if="!mod.last_user" :time="mod.bumped_at"/>
                            <span v-else class="items-center inline-flex gap-1">
                                <i18n-t keypath="by_user_time_ago">
                                    <template #user>
                                        <a-user avatar-size="xs" :user="mod.last_user"/>
                                    </template>
                                    <template #time>
                                        <time-ago :time="mod.bumped_at"/>
                                    </template>
                                </i18n-t>
                            </span>
                        </span>
                    </flex>
                </div>
                <flex class="md:ml-auto">
                    <a-button v-if="canLike" id="like-button" :color="mod.liked && 'danger' || 'secondary'" class="large-button" icon="heart" :to="!user && '/login'" @click="toggleLiked"/>
                    <a-button v-if="mod.download && mod.download_type == 'file'" class="large-button w-full text-center" icon="download" :href="!static && downloadUrl" download @click="registerDownload">
                        Download
                        <br>
                        <span class="text-sm">{{(mod.download as any).type}} - {{friendlySize((mod.download as any).size)}}</span>
                    </a-button>
                     <va-popover v-else-if="mod.download && mod.download_type == 'link'" trigger="click">
                        <template #body>
                            <div style="width: 250px;">
                                {{$t('show_download_link_warn')}}
                                <br>
                                <a :href="(mod.download as any).url">{{(mod.download as any).url}}</a>
                            </div>
                        </template>
                        <a-button class="large-button w-full text-center" icon="download" @click="registerDownload">
                            {{$t('show_download_link')}}
                        </a-button>
                    </va-popover>
                    <a-button v-else-if="mod.files && mod.files.length > 0" class="large-button" icon="download" @click="switchToFiles">Downloads</a-button>
                    <a-button v-else class="large-button" disabled>No Files</a-button>
                </flex>
            </flex>
        </flex>
    </a-banner>
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
const { public: config } = useRuntimeConfig();

const props = defineProps<{
    mod: Mod,
    static?: boolean
}>();

const { user, hasPermission } = useStore();

//Guests can't actually like the mod, it's just a redirect.
const canLike = computed(() => !user || (user.id !== props.mod.user_id && hasPermission('like-mod')));
const downloadUrl = computed(() => `${config.apiUrl}/files/${props.mod.download.id}/download`);

function registerDownload() {
    if (props.static) {
        return;
    }

    usePost(`mods/${props.mod.id}/register-download`, null, {
        async onResponse({ response }) {
            if (response.status == 201) {
                props.mod.downloads++;
            }
        }
    });
}

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
    .download-button {
        font-size: 1.5rem;
    }
</style>