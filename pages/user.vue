<template>
    <page-block :error="error" error-string="This user does not exist!">
        <a-banner :src="user.banner" url-prefix="users/banners">
            <a-avatar class="mt-auto d-inline-block" size="largest" :src="user.avatar"/>
        </a-banner>
        <flex gap="3" class="md:flex-row">
            <content-block id="details" class="p-4">
                <flex column>
                    <div id="main-info" style="min-width: 300px;">
                        <flex class="text-xl">
                            <a-user :user="user" :avatar="false"/>
                            <div v-if="!userInvisible && isPublic" id="status" :title="statusString" class="user-status my-auto" :style="{backgroundColor: statusColor}"/>
                        </flex>
                        <span v-if="!userInvisible">{{user.custom_title}}</span>
                        <!-- <span v-if="user.roletitle && user.roletitle != user.usertitle">{{user.roletitle}}</span> -->
                    </div>
                    <div v-if="isPublic" id="extra-info" style="word-break: break-word;">
                        <div v-if="user.created_at">{{$t('registration_date')}} {{fullDate(user.created_at)}}</div>
                        <div>{{$t('last_visit')}} {{timeAgo(user.last_online)}}</div>
                        <!-- <div v-if="isMod && user.strikes > 0">Strikes: {{user.strikes}}</div> -->
                        <!-- <div v-if="user.steamid && ((!user.prefs.hide_steam_link && mybb.user.uid != user.uid) || isMod)">
                            {{$t('steam_profile')}}: <a :href="`https://steamcommunity.com/profiles/${user.steamid}`" target="_blank">https://steamcommunity.com/profiles/{{user.steamid}}</a>
                        </div> -->
                        <!-- <div v-if="user.bday || user.age">
                            {{$t('date_of_birth')}} {{user.bday}} {{user.age}}
                        </div> -->
                    </div>
                </flex>
            </content-block>
            <content-block id="bio" class="p-4 w-full">
                <span class="text-lg">
                    <template v-if="isPublic">
                        <markdown v-if="user.bio" :text="user.bio"/>
                        <div v-else class="w-full">{{$t('no_bio')}}</div>
                    </template>
                    <div v-else>{{$t('private_profile_notice')}}</div>
                </span>
            </content-block>
        </flex>
        <mod-list v-if="isPublic" :user-id="user.id"/>
    </page-block>
</template>
<script setup lang="ts">
import { timeAgo, fullDate } from '../utils/helpers';
import { DateTime } from 'luxon';
import { useI18n } from 'vue-i18n';
import { User } from '../types/models';

const route = useRoute();
const { t } = useI18n();

const { data: user, error } = await useFetchData<User>(`users/${route.params.id}`);

const isOnline = computed(() => {
    const last = DateTime.fromISO(user.value.last_online);
    const now = DateTime.now();
    return now.diff(last, 'minutes').toObject().minutes < 5;
});
const statusColor = computed(() => isOnline.value ? 'green' : 'gray');
const statusString = computed(() => t(isOnline.value ? 'online' : 'offline'));
const userInvisible = computed(() => false);
const isPublic = computed(() => !user.value.private_profile);
</script>
<style>
    .user-status {
        height: 12px;
        width: 12px;
        border-radius: 1em;
        display: inline-block;
    }
</style>