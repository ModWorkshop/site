<template>
    <page-block v-if="user">
        <Title>{{user.name}}</Title>
        <flex v-if="me">
            <a-button v-if="false && !user.blocked_me"> {{$t('send_pm')}}</a-button>
            <a-report v-if="!isMe" resource-name="user" :url="`users/${user.id}/reports`"/>
            <VDropdown v-if="canModerate" arrow placement="bottom-start">
                <a-button><i-mdi-caret-down/><i-mdi-gavel/> {{$t('moderation')}}</a-button>
                <template #popper>
                    <a-dropdown-item v-if="canManageUsers" :to="`/user/${user.id}/edit`"><i-mdi-cog/> {{$t('edit')}}</a-dropdown-item>
                    <a-dropdown-item v-if="canModerateUsers" :to="`/admin/cases?user=${user.id}`"><i-mdi-alert-circle/> {{$t('warn')}}</a-dropdown-item>
                    <a-dropdown-item v-if="canModerateUsers" :to="`/admin/bans?user=${user.id}`"><i-mdi-alert/> {{$t('ban')}}</a-dropdown-item>
                    <a-dropdown-item v-if="canManageMods" @click="showDeleteAllModsModal"><i-mdi-delete/> {{$t('delete_all_mods')}}</a-dropdown-item>
                    <a-dropdown-item v-if="canManageDiscussions" @click="showDeleteDiscussionsModal"><i-mdi-delete/> {{$t('delete_all_discussions')}}</a-dropdown-item>
                </template>
            </VDropdown>
            <VDropdown placement="bottom-start">
                <a-button><i-mdi-caret-down/></a-button>
                <template #popper>
                    <template v-if="isMe || canManageDiscussions">
                        <a-dropdown-item :to="`/user/${user.unique_name || user.id}`">
                            <i-mdi-account/> {{$t('profile')}}
                        </a-dropdown-item>
                        <a-dropdown-item :to="`/user/${user.unique_name || user.id}/comments`">
                            <i-mdi-comment/> {{$t('comments')}}
                        </a-dropdown-item>
                        <a-dropdown-item :to="`/user/${user.unique_name || user.id}/threads`">
                            <i-mdi-forum/> {{$t('threads')}}
                        </a-dropdown-item>
                    </template>
                    <a-dropdown-item v-if="user.followed" @click="user.followed && setFollowUser(user, false)">
                        <i-mdi-minus-thick/> {{$t('unfollow')}}
                    </a-dropdown-item>
                    <template v-else>
                        <a-dropdown-item @click="setFollowUser(user, true)"><i-mdi-bell/> {{$t('follow_with_notifs')}}</a-dropdown-item>
                        <a-dropdown-item @click="setFollowUser(user, false)"><i-mdi-plus-thick/> {{$t('follow')}}</a-dropdown-item>
                    </template>
                    <template v-if="!isMe">
                    <a-dropdown-item v-if="isBlocked" @click="isBlocked && blockUser()">
                        <i-mdi-account-off/> {{$t('unblock')}}
                    </a-dropdown-item>
                    <template v-else>
                        <a-dropdown-item @click="blockUser">
                            <i-mdi-account-off/> {{$t(isBlocked ? 'unblock' : 'block')}}
                        </a-dropdown-item>
                        <a-dropdown-item @click="hideUserMods">
                            <i-mdi-eye-off/> {{$t(isHidingMods ? 'unhide_mods' : 'hide_mods')}}
                        </a-dropdown-item>
                    </template>
                </template>
                </template>
            </VDropdown>
        </flex>
        <NuxtPage :user="user" :trigger-refresh="triggerRefresh"/>
    </page-block>
</template>
<script setup lang="ts">
import { setFollowUser } from '~~/utils/follow-helpers';
import { useI18n } from 'vue-i18n';
import { User } from '~~/types/models';
import { useStore } from '~~/store';

const yesNoModal = useYesNoModal();
const triggerRefresh = createEventHook<void>();
const { public: config } = useRuntimeConfig();

const { t } = useI18n();

const { data: user } = await useResource<User>('user', 'users');
const thumbnail = computed(() => {
    const avatar = user.value.avatar;
    if (avatar) {
        return `${config.storageUrl}/users/images/${avatar}`;
    } else {
        return `${config.siteUrl}/assets/no-preview-dark.png`;
    }
});

useServerSeoMeta({
	ogSiteName: 'ModWorkshop - User',
	ogTitle: user.value?.name,
	ogImage: thumbnail.value,
	twitterCard: 'summary',
});

const { hasPermission, user: me } = useStore();

const isBlocked = computed(() => user.value.blocked_by_me?.silent === false);
const canModerateUsers = computed(() => hasPermission('moderate-users'));
const canManageUsers = computed(() => hasPermission('manage-users'));
const canManageMods = computed(() => hasPermission('manage-mods'));
const canManageDiscussions = computed(() => hasPermission('manage-discussions'));
const canModerate = computed(() => 
    canModerateUsers.value || 
    canManageUsers.value || 
    canManageMods.value || 
    canManageDiscussions.value
);
const isMe = computed(() => me?.id === user.value.id);

const isHidingMods = computed(() => user.value.blocked_by_me?.silent === true);
const tempBlockOverride = ref(false);

async function blockUser() {
    const block = !user.value.blocked_by_me || user.value.blocked_by_me.silent === true;

    if (block) {
        yesNoModal({
            title: t('are_you_sure'),
            desc: t('block_user_desc'),
            async yes() {
                await postRequest('blocked-users', { user_id: user.value.id, silent: false });
                
                tempBlockOverride.value = false;
                user.value.blocked_by_me = { silent: false };
            }
        });
    } else {
        await deleteRequest(`blocked-users/${user.value.id}`);
        tempBlockOverride.value = false;
        user.value.blocked_by_me = undefined;
    }

}

function hideUserMods() {
    const block = !user.value.blocked_by_me;
    yesNoModal({
        title: t('are_you_sure'),
        desc: t(block ? 'hide_user_mods_desc' : 'unhide_user_mods_desc'),
        async yes() {

            if (block) {
                await postRequest('blocked-users', { user_id: user.value.id, silent: true });
            } else {
                await deleteRequest(`blocked-users/${user.value.id}`);
            }

            tempBlockOverride.value = false;
            user.value.blocked_by_me = block ? { silent: true } : undefined;
        }
    });

}

function showDeleteAllModsModal() {
    yesNoModal({
        title: t('are_you_sure'),
        desc: 'This will delete all mods the user uploaded to the site!',
        async yes() {
            await deleteRequest(`users/${user.value.id}/mods`);
            triggerRefresh.trigger();
        }
    });
}

function showDeleteDiscussionsModal() {
    yesNoModal({
        title: t('are_you_sure'),
        desc: 'This will delete all threads and comments the user posted to the site!',
        async yes() {
            await deleteRequest(`users/${user.value.id}/discussions`);
        }
    });
}
</script>