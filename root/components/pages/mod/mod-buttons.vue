<template>
    <flex wrap>
        <NuxtLink v-if="$route.name == 'mod-mod-edit'" :to="`/mod/${mod.id}`">
            <a-button icon="arrow-left">{{$t('return_to_mod')}}</a-button>
        </NuxtLink> 
        <a-button v-else-if="canEdit" :to="`/mod/${mod.id}/edit`" icon="mdi:cog">{{$t('edit_mod')}}</a-button>
        <a-report resource-name="mod" :url="`/mods/${mod.id}/reports`"/>
        <VDropdown :disabled="mod.followed">
            <a-button :icon="mod.followed ? 'mdi:minus-thick' : 'mdi:plus-thick'" @click="mod.followed && setFollowMod(mod, false)">
                {{$t(mod.followed ? 'unfollow' : 'follow')}} <a-icon v-if="!mod.followed" icon="caret-down"/>
            </a-button>
            <template #popper>
                <a-dropdown-item @click="setFollowMod(mod, true)">{{$t('follow_mod_notifs')}}</a-dropdown-item>
                <a-dropdown-item @click="setFollowMod(mod, false)">{{$t('follow')}}</a-dropdown-item>
            </template>
        </VDropdown>
        <mod-suspend v-model:show-modal="showSuspension" :button="false" :mod="mod"/>
        <VDropdown v-if="canManage" arrow dispose-timout="0">
            <a-button icon="mdi:gavel">{{$t('moderation')}}</a-button>
            <template #popper>
                <a-dropdown-item @click="showSuspension = true">{{mod.suspended ? $t('unsuspend') : $t('suspend')}}</a-dropdown-item>
                <a-dropdown-item v-if="mod.images?.length" @click="deleteAllImages">{{$t('delete_images')}}</a-dropdown-item>
                <a-dropdown-item v-if="mod.files?.data.length" @click="deleteAllFiles">{{$t('delete_files')}}</a-dropdown-item>
            </template>
        </VDropdown>
    </flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
import { Paginator } from '../../../types/paginator';

const props = defineProps<{
    mod: Mod,
}>();

const showSuspension = ref(false);

const { hasPermission } = useStore();
const yesNoModal = useYesNoModal();
const { t } = useI18n();
const canEdit = computed(() => canEditMod(props.mod));
const canManage = computed(() => hasPermission('manage-mods', props.mod.game));

function deleteAllFiles() {
    yesNoModal({
        desc: t('delete_files_desc'),
        descType: 'warning',
        async yes() {
            await deleteRequest(`mods/${props.mod.id}/files`);
            props.mod.files = new Paginator();
            if (props.mod.download_type == 'file') {
                props.mod.download = undefined;
                props.mod.download_type = undefined;
            }
            props.mod.has_download = props.mod.links ? props.mod.links.data.length > 0 : false;
        }
    });
}

function deleteAllImages() {
    yesNoModal({
        desc: t('delete_images_desc'),
        descType: 'warning',
        async yes() {
            await deleteRequest(`mods/${props.mod.id}/images`);
            props.mod.images = [];
            props.mod.thumbnail = undefined;
            props.mod.thumbnail_id = undefined;
            props.mod.banner = undefined;
            props.mod.banner_id = undefined;
        }
    });
}
</script>