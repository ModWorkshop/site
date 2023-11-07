<template>
    <template v-if="tempBlockOverride || !isBlocked">
        <a-banner :src="user.banner" url-prefix="users/images">
            <a-avatar class="mt-auto d-inline-block mb-2 ml-2" size="3xl" :src="user.avatar"/>
        </a-banner>
        <flex gap="3" column class="details md:flex-row">
            <content-block class="p-4 place-self-start max-sm:w-full">
                <flex gap="3" column style="min-width: 300px;">
                    <a-user class="text-2xl" :user="user" :avatar="false" static show-online-state>
                        <template #details>
                            <span v-if="user.unique_name" class="user-at text-base">@{{user.unique_name}} / ID {{user.id}}</span>
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
                            <span class="text-secondary">{{getTimeAgo($t, user.last_online)}}</span>
                        </flex>
                        <flex column>
                            {{$t('mods')}}
                            <span class="text-secondary">{{user.mods_count}}</span>
                        </flex>
                        <flex v-if="user.donation_url" column>
                            {{$t('support_user')}}
                            <donation-button :link="user.donation_url"/>
                        </flex>
                        <flex column>
                            <role-selector :user="user"/>
                        </flex>
                    </template>
                </flex>
            </content-block>
            <content-block class="bio p-4 w-full">
                <span class="text-lg">
                    <template v-if="isPublic">
                        <a-markdown v-if="user.bio" :text="user.bio"/>
                        <div v-else class="w-full">{{$t('no_bio')}}</div>
                    </template>
                    <div v-else>{{$t('private_profile_notice')}}</div>
                </span>
            </content-block>
        </flex>
        <template v-if="isPublic">
            <template v-if="tempBlockOverride || !isHidingMods">
                <button-group v-model:selected="displayMods" gap="1" button-style="nav">
                    <a-group-button name="personal">{{$t('personal_mods')}}</a-group-button>
                    <a-group-button name="collab">{{$t('collab_mods')}}</a-group-button>
                </button-group>
                <mod-list 
                    v-if="isPublic || isOwnOrModerator"
                    :trigger-refresh="triggerRefresh"
                    :user-id="user.id"
                    :collab="displayMods == 'collab'"
                    :params="{ ignore_blocked_users: true }"
                />
            </template>
            <content-block v-else>
                {{$t('hiding_mods_view')}}
                <div>
                    <a-button @click="tempBlockOverride = true">{{$t('view')}}</a-button>
                </div>
            </content-block>
        </template>
    </template>
    <content-block v-else>
        {{$t('blocked_user_view')}}
        <flex>
            <a-button @click="tempBlockOverride = true">{{$t('view')}}</a-button>
            <a-button to="/">{{$t('back_to_home')}}</a-button>
        </flex>
    </content-block>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon';
import type { User } from '~~/types/models';
import { useStore } from '~~/store';
import { date, getTimeAgo } from '~~/utils/helpers';
import type { EventHook } from '@vueuse/core';

const { public: config } = useRuntimeConfig();

const { user } = defineProps<{
    user: User;
    triggerRefresh: EventHook
}>();

const thumbnail = computed(() => {
    const avatar = user.avatar;
    if (avatar) {
        return `${config.storageUrl}/users/images/${avatar}`;
    } else {
        return `${config.siteUrl}/assets/no-preview-dark.png`;
    }
});

useServerSeoMeta({
	ogSiteName: 'ModWorkshop - User',
	ogTitle: user?.name,
	ogImage: thumbnail.value,
	twitterCard: 'summary',
});

const { hasPermission, user: me } = useStore();

const canModerateUsers = computed(() => hasPermission('moderate-users'));
const isOwnOrModerator = computed(() => me && (user.id === me.id || canModerateUsers.value));
const isBlocked = computed(() => user.blocked_by_me?.silent === false);
const isHidingMods = computed(() => user.blocked_by_me?.silent === true);
const tempBlockOverride = ref(false);
const displayMods = ref('personal');

const isOnline = computed(() => {
    if (!user.last_online) {
        return false;
    }
    const last = DateTime.fromISO(user.last_online);
    const now = DateTime.now();
    return (now.diff(last, 'minutes').toObject()?.minutes ?? 0) < 5;
});
const userInvisible = computed(() => user.invisible);
const isPublic = computed(() => !user.private_profile || isOwnOrModerator.value);
</script>

<style scoped>
.bio {
    max-height: 500px;
    overflow: auto;
}
</style>