<template>
    <page-block :game="mod.game" :breadcrumb="breadcrumb">
        <Title>{{mod.name}}</Title>
        <flex v-if="!mod.has_download || mod.approved !== true || mod.suspended" column gap="2">
            <the-tag-notices :tags="mod.tags"/>
            <a-alert v-if="mod.suspended" color="danger" :title="$t('suspended')">
                <i18n-t keypath="mod_suspended" tag="span">
                    <template #reason>
                        <span v-if="mod.last_suspension">
                            {{mod.last_suspension.reason}}
                        </span>
                        <span v-else>{{$t('no_reason')}}</span>
                    </template>
                    <template #rules>
                        <NuxtLink to="/rules">{{$t('rules').toLowerCase()}}</NuxtLink>
                    </template>
                    <template #forum>
                        <NuxtLink :to="`/game/${mod.game?.short_name}/forum?category=appeals`">{{$t('forum').toLowerCase()}}</NuxtLink>
                    </template>
                </i18n-t>
            </a-alert>
            <a-alert v-if="!mod.has_download" color="warning" :title="$t('downloads_alert')" :desc="$t('downloads_alert_desc')"/>
            <a-alert v-else-if="mod.approved === null" color="info" :title="$t('mod_waiting')">
                <span>
                    {{$t('mod_waiting_desc')}}
                </span>
                <mod-approve v-if="canManage" :mod="mod"/>
            </a-alert>
            <a-alert v-else-if="mod.approved === false" color="danger" :title="$t('mod_rejected')" :desc="$t('mod_rejected_desc')"/>
        </flex>

        <flex wrap>
            <a-button v-if="canEdit" :to="`/mod/${mod.id}/edit`" icon="cog">{{$t('edit_mod')}}</a-button>
            <a-report resource-name="mod" :url="`/mods/${mod.id}/reports`"/>
            <VDropdown :disabled="mod.followed">
                <a-button :icon="mod.followed ? 'minus' : 'plus'" @click="mod.followed && setFollowMod(mod, false)">
                    {{$t(mod.followed ? 'unfollow' : 'follow')}} <font-awesome-icon v-if="!mod.followed" icon="caret-down"/>
                </a-button>
                <template #popper>
                    <a-dropdown-item @click="setFollowMod(mod, true)">{{$t('follow_mod_notifs')}}</a-dropdown-item>
                    <a-dropdown-item @click="setFollowMod(mod, false)">{{$t('follow')}}</a-dropdown-item>
                </template>
            </VDropdown>
            <a-button icon="share-nodes" @click="openShare">{{$t('share')}}</a-button>
            <VDropdown v-if="canManage" arrow>
                <a-button icon="gavel">{{$t('moderation')}}</a-button>
                <template #popper>
                    <mod-suspend :mod="mod">
                        <a-dropdown-item>{{mod.suspended ? $t('unsuspend') : $t('suspend')}}</a-dropdown-item>
                    </mod-suspend>
                    <a-dropdown-item v-if="mod.images.length" @click="deleteAllImages">{{$t('delete_images')}}</a-dropdown-item>
                    <a-dropdown-item v-if="mod.files.length" @click="deleteAllFiles">{{$t('delete_files')}}</a-dropdown-item>
                </template>
            </VDropdown>
        </flex>
        <NuxtPage :mod="mod"/>
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { canEditMod } from '~~/utils/mod-helpers';
import { setFollowMod } from '~~/utils/follow-helpers';

const { hasPermission, setGame } = useStore();
const { public: config } = useRuntimeConfig();

const { t } = useI18n();

const { data: mod } = await useResource<Mod>('mod', 'mods', {
    suspended: t('error_suspended')
});

setGame(mod.value.game);

const yesNoModal = useYesNoModal();

if (mod.value) {
    usePost(`mods/${mod.value.id}/register-view`, null, {
        async onResponse({ response }) {
            if (response.status == 201) {
                mod.value.views++;
            }
        }
    });
}

const breadcrumb = computed(() => {
    return [
        { name: t('games'), to: 'games' },
        ...mod.value.breadcrumb
    ];
});

const canEdit = computed(() => canEditMod(mod.value));
const canManage = computed(() => hasPermission('manage-mod', mod.value.game));

function openShare() {
    navigator.share({
        url: `${config.siteUrl}/${mod.value.id}`
    });
}

function deleteAllFiles() {
    yesNoModal({
        desc: t('delete_files_desc'),
        descType: 'warning',
        async yes() {
            await useDelete(`mods/${mod.value.id}/files`);
            mod.value.files = [];
            mod.value.has_download = mod.value.links.length > 0;
        }
    });
}

function deleteAllImages() {
    yesNoModal({
        desc: t('delete_images_desc'),
        descType: 'warning',
        async yes() {
            await useDelete(`mods/${mod.value.id}/images`);
            mod.value.images = [];
        }
    });
}
</script>