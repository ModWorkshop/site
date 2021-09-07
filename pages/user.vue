<template>
    <div id="user-main">
        <flex column class="user-banner px-3" :style="{backgroundImage: userBanner}">
            <avatar class="mt-auto d-inline-block" size="largest" :src="userAvatar"/>
        </flex>
        <flex :column="false" class="flex-md-row">
            <flex wrap id="details" class="content-block">
                <flex column class="ml-2">
                    <div id="main-info" style="min-width: 300px;">
                        <h4>
                            {{user.name}}
                            <div v-if="!userInvisible && profilePublic" id="status" :title="statusString" class="userStatus" :style="{backgroundColor: statusColor}"></div>
                        </h4>
                        <h6 v-if="!userInvisible">{{user.usertitle}}</h6>
                        <h6 v-if="user.roletitle && user.roletitle != user.usertitle">{{user.roletitle}}</h6>
                    </div>
                    <div v-if="profilePublic" id="extra-info" style="word-break: break-word;">
                        <div>{{$t('registration_date')}} {{user.regdate}}</div>
                        <div>{{$t('lastvisit')}} {{user.lastactive}}</div>
                        <div v-if="isMod && user.strikes > 0">Strikes: {{user.strikes}}</div>
                        <div v-if="user.steamid && ((!user.prefs.hide_steam_link && mybb.user.uid != user.uid) || isMod)">
                            {{$t('steam_profile')}}: <a :href="`https://steamcommunity.com/profiles/${user.steamid}`" target="_blank">https://steamcommunity.com/profiles/{{user.steamid}}</a>
                        </div>
                        <div v-if="user.bday || user.age">
                            {{$t('date_of_birth')}} {{user.bday}} {{user.age}}
                        </div>
                    </div>
                </flex>
            </flex>
            <div id="bio" class="ml-0 md:ml-2 content-block markdown" style="flex-grow:1;" v-html="userBio"/>
        </flex>
    </div>
</template>
<script>
import { parseMarkdown } from '../utils/md-parser';
export default {
    data() {
        return {
            user: {}
        }
    },
    computed: {
        isMod() {
            return true;
        },
        userBanner() { //TEMP!
            return 'url(https://modworkshop.net/uploads/banners/banner_11.png?t=1586915614)';
        },
        userBio() {
            return parseMarkdown(`
### I made some mods for payday now I am just lazy

I'm the main site developer at time of writing. Feel free to suggest ideas for the site in our discord server!
**I do not deal with moderation anymore. Please direct things related to that to the moderators.**

Pretty much all my mods have either no license (falls under default license of the site which will come at some point.....) or MIT license. Generally, You are free to reupload my mods; crediting would be nice but I don't mind too much. I'd love to see alternations of them!            `);
        },
        statusColor() { //TEMP!
            return 'green';
        },
        statusString() {
            return 'Online';
        },
        userInvisible() {
            return false;
        },
        profilePublic() {
            return true;
        },
        userAvatar() {
            return 'http://localhost:8001/storage/' + this.user.avatar; //TODO: don't hardcode this URL.
        }
    },
    async asyncData({params, $factory}) {
        if (params.id) {
            return {user: await $factory.getOne('users', params.id)};
        }
    }
}
</script>
<style scoped>
    #user-main {
        width: 87%;
    }

    .user-banner {
        display: flex;
        width: 100%;
        height: 300px;
        background-origin: 100% 50%;
        background-repeat: no-repeat;
        background-size:cover;
    }
    
    .userStatus {
        height: 12px;
        width: 12px;
        border-radius: 1em;
        display: inline-block;
    }
</style>