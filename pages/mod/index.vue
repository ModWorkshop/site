<template>
    <page-block v-if="mod">
        <Head>
            <Title>{{mod.name}}</Title>
        </Head>
        <the-breadcrumbs :items="mod.breadcrumb"/>
        <a-alert v-if="mod.suspended" color="danger" :title="$t('suspended')">
            <i18n-t keypath="mod_suspended" tag="span">
                <template #rules>
                    <NuxtLink to="/rules">{{$t('rules').toLowerCase()}}</NuxtLink>
                </template>
            </i18n-t>
        </a-alert>
        <a-alert v-if="mod.file_status === 0" color="warning" :title="$t('files_alert_title')" :desc="$t('files_alert')"/>
        <a-alert v-if="mod.file_status === 2" color="info" :title="$t('files_alert_waiting_title')" :desc="$t('files_alert_waiting')"/>
        <flex>
            <a-button v-if="canEdit" :to="`/mod/${mod.id}/edit`" icon="cog">{{$t('edit_mod')}}</a-button>
            <a-button color="danger">{{$t('report_mod')}}</a-button>
            <a-button>{{$t('follow')}}</a-button>
            <a-button @click="openShare">{{$t('share')}}</a-button>
        </flex>
        <div>
            <mod-banner :mod="mod"/>
        </div>
        <div class="mod-main">
            <mod-tabs :mod="mod"/>
            <mod-right-pane :mod="mod"/>
        </div>
        <the-comments 
            :url="`mods/${mod.id}/comments`" 
            :can-edit-all="canEditComments"
            :can-delete-all="canDeleteComments"
            :get-special-tag="commentSpecialTag"
            :can-comment="canComment"
        />
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod, Comment } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { canEditMod, memberLevels } from '~~/utils/mod-helpers';

const { hasPermission } = useStore();
const route = useRoute();
const { public: config } = useRuntimeConfig();

const { t } = useI18n();

const { data: mod, error } = await useFetchData<Mod>(`mods/${route.params.id}/`);
useHandleError(error.value, {
    404: "This mod doesn't exist",
    401: "You don't have permission to view this mod",
    403: "You don't have permission to view this mod"
});

if (mod.value) {
    usePost(`mods/${mod.value.id}/register-view`, null, {
        async onResponse({ response }) {
            if (response.status == 201) {
                mod.value.views++;
            }
        }
    });
}

const canEdit = computed(() => canEditMod(mod.value));
const canEditComments = computed(() => hasPermission('edit-comment'));
const canDeleteComments = computed(() => canEditComments.value || (canEdit.value && hasPermission('delete-own-mod-comment')));
const canComment = computed(() => !mod.value.comments_disabled || canEdit.value);

function commentSpecialTag(comment: Comment) {
    if (comment.user_id === mod.value.user_id) {
        return `${t('owner')}`;
    } else {
        const member = mod.value.members.find(member => comment.user_id === member.id);
        if (member) {
            return memberLevels[member.level];
        }
    }
}

function openShare() {
    navigator.share({
        url: `${config.siteUrl}/${mod.value.id}`
    });
}
</script>

<style>
    .mod-banner {
        box-shadow: 1px 1px 5px #000;
        box-shadow: inset 0px 0px 30px 20px rgba(0,0,0, 0.45);
    }

    .mod-data {
        height: 100%;
        color: var(--MAIN_COLOR_TEXT);
        padding: 0.75rem;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0));
    }

    .mod-data-top {
        overflow: hidden;
        height: 148px;
        word-break: break-word;
    }

    .mod-banner #title {
        font-size: 2rem;
    }

    .mod-banner .data .version {
        font-weight: normal;
    }

    .mod-main {
        display: grid;
        grid-gap: .75rem;
        margin-right: .75rem;
        grid-template-columns: 70% 30%
    }

    .desc-content img {
        max-width: 100%;
    }

    .fixed-anchor {
        position: relative;
        top: -64px;
    }

    @media (min-width:600px) and (max-width:850px) {
        .mod-info .thumbnail {
            display: none;
        }
    }

    @media (max-width:850px) {
        .mod-banner {
            height: 295px;
        }

        .mod-info {
            order: -1;
        }

        .mod-main {
            grid-template-columns: auto;
            margin-right: 0;
        }
        .contributor-block .info{
            line-height: 32px;
        }
        .contributor-block .avatar {
            height: 64px;
            width: 64px;
        }
    }

    .large-button {
        font-size: 1.5rem;
        padding: 0.5rem 1.5rem !important;
    }


    .mod-banner a > span {
        text-shadow: 1px 1px rgba(0, 0, 0, 0.3);
    }
</style>