<template>
    <page-block :game="mod.game" :breadcrumb="breadcrumb">
        <Title>{{mod.name}}</Title>
        <flex v-if="notices.length || mod.suspended" column gap="2">
            <a-alert v-for="notice of notices" :key="notice.id" :color="notice.type" :desc="notice.localized ? $t(notice.notice) : notice.notice"/>
            <a-alert v-if="mod.suspended" color="danger" :title="$t('suspended')">
                <i18n-t keypath="mod_suspended" tag="span">
                    <template #reason>
                        <span v-if="mod.last_suspension">
                            {{mod.last_suspension.reason}}
                        </span>
                        <span v-else>
                            No reason stated.
                        </span>
                    </template>
                    <template #rules>
                        <NuxtLink to="/rules">{{$t('rules').toLowerCase()}}</NuxtLink>
                    </template>
                    <template #forum>
                        <NuxtLink :to="`/game/${mod.game?.short_name}/forum?category=appeals`">{{$t('forum').toLowerCase()}}</NuxtLink>
                    </template>
                </i18n-t>
            </a-alert>
        </flex>

        <a-alert v-if="!mod.has_download" color="warning" :title="$t('files_alert_title')" :desc="$t('files_alert')"/>
        <a-alert v-else-if="mod.approved === null" color="info" :title="$t('mod_waiting')">
            <span>
                {{$t('mod_waiting_desc')}}
            </span>
            <mod-approve v-if="canManage" :mod="mod"/>
        </a-alert>
        <a-alert v-else-if="mod.approved === false" color="danger" :title="$t('mod_rejected')" :desc="$t('mod_rejected_desc')"/>
        <flex>
            <a-button v-if="canEdit" :to="`/mod/${mod.id}/edit`" icon="cog">{{$t('edit_mod')}}</a-button>
            <a-report resource-name="mod" :url="`/mods/${mod.id}/reports`"/>
            <VDropdown :disabled="mod.followed">
                <a-button :icon="mod.followed ? 'minus' : 'plus'" @click="mod.followed && setFollowMod(mod, false)">
                    {{$t(mod.followed ? 'unfollow' : 'follow')}} <font-awesome-icon v-if="!mod.followed" icon="caret-down"/>
                </a-button>
                <template #popper>
                    <a-dropdown-item @click="setFollowMod(mod, true)">Follow and get notified for updates</a-dropdown-item>
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
    suspended: t('suspended_error')
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

const notices = computed(() => {
    const notices: { id: number, type: string, notice: string, localized: boolean }[] = [];
    for (const tag of mod.value.tags) {
        if (tag.notice && tag.notice.length > 0 && notices.length < 2) {
            notices.push({ id: tag.id, type: tag.notice_type, notice: tag.notice, localized: tag.notice_localized });
        }
    }

    return notices;
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
        desc: 'This will delete all files of the mod, this cannot be reversed!',
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
        desc: 'This will delete all images of the mod, this cannot be reversed!',
        descType: 'warning',
        async yes() {
            await useDelete(`mods/${mod.value.id}/images`);
            mod.value.images = [];
        }
    });
}
</script>