<template>
    <page-block>
        <flex column class="user-banner p-2 round" :style="{backgroundImage: `url(http://localhost:8000/storage/${userBanner})`}">
            <a-avatar class="mt-auto d-inline-block" size="largest" :src="user.avatar"/>
        </flex>
        <flex :column="false" gap="3" class="md:flex-row">
            <content-block id="details" class="p-4">
                <flex column>
                    <div id="main-info" style="min-width: 300px;">
                        <h4>
                            <a-user :user="user" :avatar="false"/>
                            <div v-if="!userInvisible && isPublic" id="status" :title="statusString" class="user-status" :style="{backgroundColor: statusColor}"></div>
                        </h4>
                        <span v-if="!userInvisible">{{user.custom_title}}</span>
                        <span v-if="user.roletitle && user.roletitle != user.usertitle">{{user.roletitle}}</span>
                    </div>
                    <div v-if="isPublic" id="extra-info" style="word-break: break-word;">
                        <div v-if="user.created_at">{{$t('registration_date')}} {{fullDate(user.created_at)}}</div>
                        <div>{{$t('lastvisit')}} {{timeAgo(user.last_online)}}</div>
                        <div v-if="isMod && user.strikes > 0">Strikes: {{user.strikes}}</div>
                        <div v-if="user.steamid && ((!user.prefs.hide_steam_link && mybb.user.uid != user.uid) || isMod)">
                            {{$t('steam_profile')}}: <a :href="`https://steamcommunity.com/profiles/${user.steamid}`" target="_blank">https://steamcommunity.com/profiles/{{user.steamid}}</a>
                        </div>
                        <div v-if="user.bday || user.age">
                            {{$t('date_of_birth')}} {{user.bday}} {{user.age}}
                        </div>
                    </div>
                </flex>
            </content-block>
            <content-block id="bio" class="p-4" style="flex-grow:1;">
                <markdown v-if="isPublic" :text="user.bio"/>
                <div v-else>
                    This profile is private
                </div>
            </content-block>
        </flex>
    </page-block>
</template>
<script>
import { timeAgo, fullDate } from '../utils/helpers';
import { DateTime } from 'luxon';

export default {
    data() {
        return {
            user: {}
        };
    },
    computed: {
        isMod() {
            return true;
        },
        userBanner() {
            return this.user.banner || 'banners/default_banner.webp';
        },
        isOnline() {
            const last = DateTime.fromISO(this.user.last_online);
            const now = DateTime.now();
            return now.diff(last, 'minutes').toObject().minutes < 5;
        },
        statusColor() {
            return this.isOnline ? 'green' : 'gray';
        },
        statusString() {
            return this.isOnline ? this.$t('online') : this.$t('offline');
        },
        userInvisible() {
            return false;
        },
        isPublic() {
            return !this.user.private_profile;
        },
    },
    methods: {
        timeAgo, fullDate
    },
    async asyncData({params, $factory}) {
        if (params.id) {
            return {user: await $factory.getOne('users', params.id)};
        }
    }
};
</script>
<style scoped>
    .user-status {
        height: 12px;
        width: 12px;
        border-radius: 1em;
        display: inline-block;
    }
</style>