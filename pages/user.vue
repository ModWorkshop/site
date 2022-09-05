<template>
    <page-block v-if="user">
        <flex v-if="authUser && user.id != authUser.id">
            <a-button v-if="!user.blocked_me" icon="message">{{$t('send_pm')}}</a-button>
            <a-button icon="bullhorn">{{$t('report')}}</a-button>
            <Popper :disabled="user.followed">
                <a-button :icon="user.followed ? 'minus' : 'plus'" @click="user.followed && follow()">
                    {{$t(user.followed ? 'unfollow' : 'follow')}} <font-awesome-icon v-if="!user.followed" icon="caret-down"/>
                </a-button>
                <template #content>
                    <a-dropdown-item @click="follow(true)">Follow and get notified for new mods</a-dropdown-item>
                    <a-dropdown-item @click="follow(false)">{{$t('follow')}}</a-dropdown-item>
                </template>
            </Popper>
            <a-button icon="user-xmark" title="Entirely blocks communication with the user and hides their mods" @click="blockUser">{{$t(isBlocked ? 'unblock' : 'block')}}</a-button>
            <a-button v-if="!isBlocked" icon="eye-slash" title="Only hides their mods" @click="hideUserMods">{{$t(isHidingMods ? 'unhide_mods' : 'hide_mods')}}</a-button>
            <Popper v-if="canModerateUser" arrow>
                <a-button icon="gavel">{{$t('moderation')}}</a-button>
                <template #content>
                    <a-dropdown-item icon="cog">{{$t('edit')}}</a-dropdown-item>
                    <a-dropdown-item icon="circle-exclamation">{{$t('warn')}}</a-dropdown-item>
                    <a-dropdown-item icon="triangle-exclamation">{{$t('ban')}}</a-dropdown-item>
                </template>
            </Popper>
        </flex>
        <template v-if="tempBlockOverride || !isBlocked">
            <a-banner :src="user.banner" url-prefix="users/banners">
                <a-avatar class="mt-auto d-inline-block mb-2 ml-2" size="2xl" :src="user.avatar"/>
            </a-banner>
            <flex gap="3" class="md:flex-row">
                <content-block id="details" class="p-4">
                    <flex column>
                        <flex column style="min-width: 300px;">
                            <a-user class="text-2xl" :user="user" :avatar="false" static>
                                <template #after-name>
                                    <div v-if="!userInvisible && isPublic" :title="statusString" class="user-status" :style="{backgroundColor: statusColor}"/>
                                </template>
                            </a-user>
                            <span v-if="!userInvisible">{{user.custom_title}}</span>
                        </flex>
                        <flex v-if="isPublic" gap="2" column class="mt-1">
                            <div v-if="user.created_at">{{$t('registration_date')}} {{fullDate(user.created_at)}}</div>
                            <div>{{$t('last_visit')}} {{timeAgo(user.last_online)}}</div>
                            <!-- <div v-if="isMod && user.strikes > 0">Strikes: {{user.strikes}}</div> -->
                            <!-- <div v-if="user.steamid && ((!user.prefs.hide_steam_link && mybb.user.uid != user.uid) || isMod)">
                                {{$t('steam_profile')}}: <a :href="`https://steamcommunity.com/profiles/${user.steamid}`" target="_blank">https://steamcommunity.com/profiles/{{user.steamid}}</a>
                            </div> -->
                            <donation-button v-if="user.donation_url" :link="user.donation_url"/>
                        </flex>
                    </flex>
                </content-block>
                <content-block id="bio" class="p-4 w-full">
                    <span class="text-lg">
                        <template v-if="isPublic || isOwnOrModerator">
                            <a-markdown v-if="user.bio" :text="user.bio"/>
                            <div v-else class="w-full">{{$t('no_bio')}}</div>
                        </template>
                        <div v-else>{{$t('private_profile_notice')}}</div>
                    </span>
                </content-block>
            </flex>
            <template v-if="tempBlockOverride || !isHidingMods">
                <mod-list v-if="isPublic || isOwnOrModerator" :user-id="user.id"/>
            </template>
            <content-block v-else>
                You've hid the user's mods. Do you wish to view their mods?
                <div>
                    <a-button @click="tempBlockOverride = true">View</a-button>
                </div>
            </content-block>
        </template>
        <content-block v-else>
            You've blocked this user. Do you wish to view their profile?
            <flex>
                <a-button @click="tempBlockOverride = true">View</a-button>
                <a-button to="/">Back Home</a-button>
            </flex>
        </content-block>
    </page-block>
</template>
<script setup lang="ts">
import { timeAgo, fullDate } from '../utils/helpers';
import { DateTime } from 'luxon';
import { useI18n } from 'vue-i18n';
import { User } from '../types/models';
import { useStore } from '~~/store';

const yesNoModal = useYesNoModal();

const { t } = useI18n();

const { data: user } = await useResource<User>('user', 'users');

if (!user.value) {
    throw createError({ statusCode: 404, statusMessage: 'bruh' });
}

const { hasPermission, user: authUser } = useStore();

const canModerateUser = computed(() => hasPermission('edit-user'));
const isOwnOrModerator = computed(() => user.value.id === authUser.id || canModerateUser.value);
const isBlocked = computed(() => user.value.blocked_by_me?.silent === false);
const isHidingMods = computed(() => user.value.blocked_by_me?.silent === true);
const tempBlockOverride = ref(false);

const isOnline = computed(() => {
    const last = DateTime.fromISO(user.value.last_online);
    const now = DateTime.now();
    return now.diff(last, 'minutes').toObject().minutes < 5;
});
const statusColor = computed(() => isOnline.value ? 'green' : 'gray');
const statusString = computed(() => t(isOnline.value ? 'online' : 'offline'));
const userInvisible = computed(() => false);
const isPublic = computed(() => !user.value.private_profile);

async function follow(notify?: boolean) {
    try {
        if (!user.value.followed) {
            await usePost('followed-users', { user_id: user.value.id, notify });
            user.value.followed = { notify: false };
        } else {
            await useDelete(`followed-users/${user.value.id}`);
            user.value.followed = null;
        }
    } catch (error) {
        console.log(error);
    }
}

async function blockUser() {
    const block = !user.value.blocked_by_me || user.value.blocked_by_me.silent === true;

    yesNoModal({
        title: 'Are you sure?',
        desc: 'This will block all communication with the user and hide their mods from showing up',
        async yes() {
            if (block) {
                await usePost('blocked-users', { user_id: user.value.id, silent: false });
            } else {
                await useDelete(`blocked-users/${user.value.id}`);
            }
            
            tempBlockOverride.value = false;
            user.value.blocked_by_me = block ? { silent: false } : null;
        }
    });

}

function hideUserMods() {
    yesNoModal({
        title: 'Are you sure?',
        desc: "This will hide the user's mods from showing up",
        async yes() {
            const block = !user.value.blocked_by_me;

            if (block) {
                await usePost('blocked-users', { user_id: user.value.id, silent: true });
            } else {
                await useDelete(`blocked-users/${user.value.id}`);
            }

            tempBlockOverride.value = false;
            user.value.blocked_by_me = block ? { silent: true } : null;
        }
    });

}
</script>
<style>
.user-status {
    height: 12px;
    width: 12px;
    border-radius: 1em;
    display: inline-block;
}
</style>