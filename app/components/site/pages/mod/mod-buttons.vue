<template>
    <mod-download-buttons :mod="mod" :download="mod.download" :type="downloadType">
        <template v-if="!mod.download">
            <m-button v-if="mod.files_count || mod.links_count || (mod.files && mod.files.data.length) || (mod.links && mod.links.data.length)" class="large-button flex-1" @click="switchToFiles">{{$t('downloads')}}</m-button>
            <m-button v-else class="large-button flex-1" disabled><i-mdi-download/> {{$t('no_downloads')}}</m-button>
        </template>
        <m-button 
            :disabled="!canLike"
            :color="mod.liked && 'danger' || 'secondary'"
            class="large-button"
            :title="$t('like_mod')"
            :to="!user ? '/login' : undefined"
            @click="toggleLiked"
        >
            <i-mdi-heart/> {{ likes }}
        </m-button>
    </mod-download-buttons>
</template>

<script setup lang="ts">
import type { Mod } from '~/types/models';
import { useStore } from '~/store';

const props = defineProps<{
    mod: Mod,
    static?: boolean
}>();

const { user, hasPermission } = useStore();
const i18n = useI18n();

//Guests can't actually like the mod, it's just a redirect.
const canLike = computed(() => !user || (user.id !== props.mod.user_id && hasPermission('like-mods', props.mod.game)));
const locale = computed(() => i18n.locale.value);
const likes = computed(() => friendlyNumber(locale.value, props.mod.likes));

const downloadType = computed(() => {
    if (props.mod.download_type) {
        return props.mod.download_type;
    } else if (props.mod.download) {
        return Object.hasOwn(props.mod.download, 'file') ? 'file' : 'link';
    }
});

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