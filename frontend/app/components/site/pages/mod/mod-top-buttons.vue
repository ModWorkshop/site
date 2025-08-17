<template>
    <m-flex class="overflow-auto">
        <m-flex class="flex-shrink-0">
            <NuxtLink v-if="$route.name == 'mod-mod-edit' || $route.name == 'upload' || $route.name == 'g-game-upload'" :to="`/mod/${mod.id}`" external>
                <m-button><i-mdi-arrow-left/> {{$t('return_to_mod')}}</m-button>
            </NuxtLink> 
            <m-button v-else-if="canEdit" :to="`/mod/${mod.id}/edit`"><i-mdi-cog/> {{$t('edit_mod')}}</m-button>
            <m-dropdown :disabled="!!mod.followed">
                <m-button @click="mod.followed && setFollowMod(mod, false)">
                    <i-mdi-minus-thick v-if="mod.followed"/>
                    <i-mdi-plus-thick v-else/>
                    {{$t(mod.followed ? 'unfollow' : 'follow')}}
                </m-button>
                <template #content>
                    <m-dropdown-item @click="setFollowMod(mod, true)">{{$t('follow_mod_notifs')}}</m-dropdown-item>
                    <m-dropdown-item @click="setFollowMod(mod, false)">{{$t('follow')}}</m-dropdown-item>
                </template>
            </m-dropdown>
            <report-button resource-name="mod" :url="`/mods/${mod.id}/reports`"/>
            <mod-suspend v-model:show-modal="showSuspension" :button="false" :mod="mod"/>
            <m-dropdown v-if="canManage">
                <m-button><i-mdi-gavel/> {{$t('moderation')}}</m-button>
                <template #content>
                    <m-dropdown-item @click="showSuspension = true">{{mod.suspended ? $t('unsuspend') : $t('suspend')}}</m-dropdown-item>
                    <m-dropdown-item v-if="mod.images?.length" @click="deleteAllImages">{{$t('delete_images')}}</m-dropdown-item>
                    <m-dropdown-item v-if="mod.files?.data.length" @click="deleteAllFiles">{{$t('delete_files')}}</m-dropdown-item>
                </template>
            </m-dropdown>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~/store';
import type { Mod } from '~/types/models';
import { Paginator } from '~/types/paginator';

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