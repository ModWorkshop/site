<template>
    <a-banner class="mod-banner" :src="mod.legacy_banner_url || (mod.banner && mod.banner.file)" height="250" url-prefix="mods/images">
        <flex column class="mod-data">
            <div class="mod-data-top">
                <span id="title">{{mod.name}}</span>
                <br>
                <span v-if="mod.publish_date">
                    <strong>{{$t('submitted')}}</strong> <span :title="mod.publish_date">{{publishDateAgo}}</span>
                </span>
            </div>
            <flex column class="mt-auto md:flex-row">
                <div class="p-0 version mt-auto">
                    <flex gap="2">
                        <strong v-if="modStatus">{{modStatus}}</strong>
                        <strong v-if="mod.version && mod.version.length <= 24">{{$t('version')}} {{mod.version}}</strong>
                        <strong>{{$t('last_updated')}}</strong> <span :title="mod.bump_date">{{updateDateAgo}}</span>
                    </flex>
                </div>
                <flex class="md:ml-auto">
                    <a-button v-if="canLike" id="like-button" :color="mod.liked && 'danger' || 'secondary'" class="large-button" icon="heart" @click="toggleLiked"/>
                    <a-button v-if="mod.download" class="large-button w-full text-center" icon="download" :href="!static && `http://localhost:8000/files/${mod.download.id}/download`" download @click="registerDownload">
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
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
import { friendlySize, timeAgo } from '~~/utils/helpers';

const props = defineProps<{
    mod: Mod,
    static: boolean
}>();

const publishDateAgo = computed(() => timeAgo(props.mod.publish_date));
const updateDateAgo = computed(() => timeAgo(props.mod.bump_date));
const modStatus = computed(() => '');

const store = useStore();

//Guests can't actually like the mod, it's just a redirect.
const canLike = computed(() => !store.user || store.user.id !== props.mod.submitter_id);

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
</script>
<style>
    
</style>