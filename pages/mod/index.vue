<template>
    <page-block v-if="mod">
        <a-modal-form v-model="showSuspendModal" :title="mod.suspended ? $t('unsuspend') : $t('suspend')" save-button="" size="medium" @save="suspend">
            <a-input v-model="suspendForm.reason" label="reason" type="textarea" rows="6"/>
            <a-input v-model="suspendForm.notify" label="Notify owner and members" type="checkbox"/>
        </a-modal-form>
        <Head>
            <Title>{{mod.name}}</Title>
        </Head>
        <the-breadcrumb :items="mod.breadcrumb"/>
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

        <a-alert v-if="mod.file_status === 0" color="warning" :title="$t('files_alert_title')" :desc="$t('files_alert')"/>
        <a-alert v-if="mod.file_status === 2" color="info" :title="$t('files_alert_waiting_title')" :desc="$t('files_alert_waiting')"/>
        <flex>
            <a-button v-if="canEdit" :to="`/mod/${mod.id}/edit`" icon="cog">{{$t('edit_mod')}}</a-button>
            <a-button color="danger">{{$t('report_mod')}}</a-button>
            <Popper :disabled="mod.followed">
                <a-button :icon="mod.followed ? 'minus' : 'plus'" @click="mod.followed && follow()">
                    {{$t(mod.followed ? 'unfollow' : 'follow')}} <font-awesome-icon v-if="!mod.followed" icon="caret-down"/>
                </a-button>
                <template #content>
                    <a-dropdown-item @click="follow(true)">Follow and get notified for updates</a-dropdown-item>
                    <a-dropdown-item @click="follow(false)">{{$t('follow')}}</a-dropdown-item>
                </template>
            </Popper>
            <a-button @click="openShare">{{$t('share')}}</a-button>
            <Popper arrow>
                <a-button icon="gavel">{{$t('moderation')}}</a-button>
                <template #content>
                    <a-dropdown-item @click="showSuspendModal = true">{{mod.suspended ? $t('unsuspend') : $t('suspend')}}</a-dropdown-item>
                    <a-dropdown-item v-if="mod.images.length" @click="deleteAllImages">{{$t('delete_images')}}</a-dropdown-item>
                    <a-dropdown-item v-if="mod.files.length" @click="deleteAllFiles">{{$t('delete_files')}}</a-dropdown-item>
                </template>
            </Popper>
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
            :cannot-comment-reason="cannotCommentReason"
        />
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod, Comment } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { canEditMod, memberLevels } from '~~/utils/mod-helpers';

const { hasPermission, isBanned } = useStore();
const { public: config } = useRuntimeConfig();

const { t } = useI18n();

const { data: mod } = await useResource<Mod>('mod', 'mods', {
    suspended: t('suspended_error')
});

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

const canEdit = computed(() => canEditMod(mod.value));
const canEditComments = computed(() => hasPermission('edit-comment'));
const canDeleteComments = computed(() => canEditComments.value || (canEdit.value && hasPermission('delete-own-mod-comment')));
const canComment = computed(() => !mod.value.user.blocked_me && !isBanned && (!mod.value.comments_disabled || canEdit.value));
const cannotCommentReason = computed(() => {
    if (mod.value.comments_disabled) {
        return t('comments_disabled');
    }

    if (isBanned) {
        return 'Banned users cannot post comments';
    }

    if (mod.value.user.blocked_me) {
        return 'You cannot comment on the mod because the owner blocked you.';
    }
});

const showSuspendModal = ref(false);
const suspendForm = reactive({
    status: computed(() => !mod.value.suspended),
    reason: '',
    notify: true
});

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

async function suspend(ok, onError) {
    try {
        await usePatch(`mods/${mod.value.id}/suspended`, suspendForm);
    
        mod.value.suspended = !mod.value.suspended;
        suspendForm.reason = '';
        ok();
    } catch (error) {
        onError(error);
    }
}

function deleteAllFiles() {
    yesNoModal({
        desc: 'This will delete all files of the mod, this cannot be reversed!',
        descType: 'warning',
        async yes() {
            await useDelete(`mods/${mod.value.id}/files`);
            mod.value.files = [];
            mod.value.file_status = 0;
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

async function follow(notify?: boolean) {
    try {
        if (!mod.value.followed) {
            await usePost('followed-mods', { mod_id: mod.value.id, notify });
            mod.value.followed = { notify: false };
        } else {
            await useDelete(`followed-mods/${mod.value.id}`);
            mod.value.followed = null;
        }
    } catch (error) {
        console.log(error);
    }
}
</script>

<style scoped>
.mod-main {
    display: grid;
    grid-gap: .75rem;
    margin-right: .75rem;
    grid-template-columns: 70% 30%
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
</style>