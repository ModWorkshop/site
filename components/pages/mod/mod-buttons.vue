<template>
    <flex wrap>
        <a-button v-if="canEdit" :to="`/mod/${mod.id}/edit`" icon="ic:baseline-settings">{{$t('edit_mod')}}</a-button>
        <a-report resource-name="mod" :url="`/mods/${mod.id}/reports`"/>
        <VDropdown :disabled="mod.followed">
            <a-button :icon="mod.followed ? 'minus' : 'plus'" @click="mod.followed && setFollowMod(mod, false)">
                {{$t(mod.followed ? 'unfollow' : 'follow')}} <a-icon v-if="!mod.followed" icon="caret-down"/>
            </a-button>
            <template #popper>
                <a-dropdown-item @click="setFollowMod(mod, true)">{{$t('follow_mod_notifs')}}</a-dropdown-item>
                <a-dropdown-item @click="setFollowMod(mod, false)">{{$t('follow')}}</a-dropdown-item>
            </template>
        </VDropdown>
        <VDropdown v-if="canManage" arrow>
            <a-button icon="gavel">{{$t('moderation')}}</a-button>
            <template #popper>
                <mod-suspend :mod="mod">
                    <a-dropdown-item>{{mod.suspended ? $t('unsuspend') : $t('suspend')}}</a-dropdown-item>
                </mod-suspend>
                <a-dropdown-item v-if="mod.images?.length" @click="deleteAllImages">{{$t('delete_images')}}</a-dropdown-item>
                <a-dropdown-item v-if="mod.files?.length" @click="deleteAllFiles">{{$t('delete_files')}}</a-dropdown-item>
            </template>
        </VDropdown>
    </flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';

const props = defineProps<{
    mod: Mod,
}>();

const { hasPermission } = useStore();
const yesNoModal = useYesNoModal();
const { t } = useI18n();
const canEdit = computed(() => canEditMod(props.mod));
const canManage = computed(() => hasPermission('manage-mod', props.mod.game));

function deleteAllFiles() {
    yesNoModal({
        desc: t('delete_files_desc'),
        descType: 'warning',
        async yes() {
            await useDelete(`mods/${props.mod.id}/files`);
            props.mod.files = [];
            props.mod.has_download = props.mod.links ? props.mod.links.length > 0 : false;
        }
    });
}

function deleteAllImages() {
    yesNoModal({
        desc: t('delete_images_desc'),
        descType: 'warning',
        async yes() {
            await useDelete(`mods/${props.mod.id}/images`);
            props.mod.images = [];
        }
    });
}
</script>