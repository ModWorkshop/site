<template>
    <page-block>
        <Title>{{user.name}}</Title>
        <flex v-if="me && user.id != me.id">
            <a-button v-if="!user.blocked_me" icon="message">{{$t('send_pm')}}</a-button>
            <a-report resource-name="user" :url="`users/${user.id}/reports`"/>
            <VDropdown :disabled="user.followed">
                <a-button :icon="user.followed ? 'minus' : 'plus'" @click="user.followed && setFollowUser(user, false)">
                    {{$t(user.followed ? 'unfollow' : 'follow')}} <font-awesome-icon v-if="!user.followed" icon="caret-down"/>
                </a-button>
                <template #popper>
                    <a-dropdown-item @click="setFollowUser(user, true)">Follow and get notified for new mods</a-dropdown-item>
                    <a-dropdown-item @click="setFollowUser(user, false)">{{$t('follow')}}</a-dropdown-item>
                </template>
            </VDropdown>
            <VDropdown :disabled="isBlocked">
                <a-button icon="user-xmark" @click="isBlocked && blockUser()">{{isBlocked ? $t('unblock') : `${$t('block')}/${$t('hide_mods')}`}}</a-button>
                <template #popper>
                    <a-dropdown-item icon="user-xmark" @click="blockUser">{{$t(isBlocked ? 'unblock' : 'block')}}</a-dropdown-item>
                    <a-dropdown-item icon="eye-slash" @click="hideUserMods">{{$t(isHidingMods ? 'unhide_mods' : 'hide_mods')}}</a-dropdown-item>
                </template>
            </VDropdown>
            <VDropdown v-if="canModerateUser" arrow>
                <a-button icon="gavel">{{$t('moderation')}}</a-button>
                <template #popper>
                    <a-dropdown-item :to="`/user/${user.id}/edit`" icon="cog">{{$t('edit')}}</a-dropdown-item>
                    <a-dropdown-item icon="circle-exclamation">{{$t('warn')}}</a-dropdown-item>
                    <a-dropdown-item icon="triangle-exclamation">{{$t('ban')}}</a-dropdown-item>
                </template>
            </VDropdown>
        </flex>
        <template v-if="tempBlockOverride || !isBlocked">
            <a-banner :src="user.banner" url-prefix="users/banners">
                <a-avatar class="mt-auto d-inline-block mb-2 ml-2" size="2xl" :src="user.avatar"/>
            </a-banner>
            <flex gap="3" class="md:flex-row">
                <content-block id="details" class="p-4">
                    <flex gap="3" column style="min-width: 300px;">
                        <a-user class="text-2xl" :user="user" :avatar="false" static>
                            <template #after-name>
                                <div v-if="!userInvisible && isPublic" :title="statusString" class="user-status" :style="{backgroundColor: statusColor}"/>
                            </template>
                            <template #details>
                                <span v-if="!userInvisible" class="text-base">{{user.custom_title}}</span>
                            </template>
                        </a-user>
                        <template v-if="isPublic">
                            <flex v-if="user.created_at" column>
                                {{$t('registration_date')}} 
                                <span class="text-secondary">{{date(user.created_at)}}</span>
                            </flex>
                            <flex v-if="!isOnline" column>
                                {{$t('last_visit')}} 
                                <span class="text-secondary">{{timeAgo(user.last_online)}}</span>
                            </flex>
                            <flex column>
                                Mods
                                <span class="text-secondary">{{mods?.meta.total}}</span>
                            </flex>
                            <flex v-if="user.donation_url" column>
                                Support User
                                <donation-button :link="user.donation_url"/>
                            </flex>
                            <flex column>
                                {{$t('roles')}}
                                <a-tag-selector
                                    v-model="user.role_ids"
                                    multiple
                                    url="roles"
                                    :color-by="item => item.color"
                                    :fetch-params="{ only_assignable: 1 }"
                                    :disabled="user.id !== me?.id && !hasPermission('manage-roles')"
                                    :enabled-by="role => role.assignable"
                                    @update:model-value="prepareSaveRoles"
                                />
                            </flex>
                        </template>
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
                <mod-list v-if="isPublic || isOwnOrModerator" :user-id="user.id" @fetched="items => mods = items"/>
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
import { setFollowUser } from '../utils/follow-helpers';
import { DateTime } from 'luxon';
import { useI18n } from 'vue-i18n';
import { Mod, User } from '../types/models';
import { useStore } from '~~/store';
import { Paginator } from '~~/types/paginator';

const yesNoModal = useYesNoModal();

const { t } = useI18n();

const { data: user } = await useResource<User>('user', 'users');

if (!user.value) {
    throw createError({ statusCode: 404, statusMessage: 'bruh' });
}

const { hasPermission, user: me } = useStore();

const canModerateUser = computed(() => hasPermission('moderate-users'));
const isOwnOrModerator = computed(() => user.value.id === me.id || canModerateUser.value);
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
const userInvisible = computed(() => user.value.invisible);
const isPublic = computed(() => !user.value.private_profile);
const mods = ref<Paginator<Mod>>(null);

const { start: prepareSaveRoles } = useTimeoutFn(saveRoles, 500, { immediate: false });
async function saveRoles() {
    await usePatch(`users/${user.value.id}/roles`, { role_ids: user.value.role_ids });
}

async function blockUser() {
    const block = !user.value.blocked_by_me || user.value.blocked_by_me.silent === true;

    if (block) {
        yesNoModal({
            title: 'Are you sure?',
            desc: 'This will block all communication with the user and hide their mods from showing up',
            async yes() {
                await usePost('blocked-users', { user_id: user.value.id, silent: false });
                
                tempBlockOverride.value = false;
                user.value.blocked_by_me = { silent: false };
            }
        });
    } else {
        await useDelete(`blocked-users/${user.value.id}`);
        tempBlockOverride.value = false;
        user.value.blocked_by_me = null;
    }

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