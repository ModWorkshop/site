<template>
    <flex column class="self-start mod-info-holder">
        <flex column gap="3" class="mod-info content-block">
            <mod-thumbnail :thumbnail="mod.thumbnail" prefer-hq/>
            <flex column class="p-4 pt-0 flex-1" gap="4">
                <flex class="text-lg flex-wrap" gap="2">
                    <span>
                        <i-mdi-heart/> {{likes}}
                    </span>
                    <span>
                        <i-mdi-download/> {{downloads}}
                    </span>
                    <span>
                        <i-mdi-eye/> {{views}}
                    </span>
                    <span v-if="mod.published_at" class="ml-auto">
                        <span :title="$t('published_at')"><i-mdi-calendar-import/></span>
                        <time-ago :time="mod.published_at"/>
                    </span>
                    <span v-else-if="mod.created_at" class="ml-auto">
                        <span :title="$t('published_at')"><i-mdi-calendar-plus/></span>
                        <time-ago :time="mod.created_at"/>
                    </span>
                </flex>
    
                <flex v-if="mod.tags && mod.tags.length > 0" wrap>
                    <a-tag v-for="tag in mod.tags" :key="tag.id" :color="tag.color">
                        <NuxtLink class="no-hover" :to="`${tagLink}?selected-tags=${tag.id}`">{{tag.name}}</NuxtLink>
                    </a-tag>
                </flex>
    
                <flex class="colllaborators-block" column>
                    <flex wrap>
                        <a-user :user="mod.user" :details="$t('owner')"/>
                        <donation-button v-if="ownerDonation" class="ml-auto" :link="ownerDonation"/>
                    </flex>
    
                    <flex v-for="member of members" :key="member.id">
                        <a-user :user="member" :details="$t(`member_level_${member.level}`)"/>
                        <donation-button v-if="member.donation_url" class="ml-auto" :link="member.donation_url"/>
                    </flex>
                </flex>
            </flex>
        </flex>
        <!-- <div id="div-gpt-ad-mws-4" class="ad mt-1" style="max-height: 400px;"/> -->
        <div id="mws-ads-mod-pane" class="ad mt-1"/>
    </flex>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';
const props = defineProps<{
    mod: Mod
}>();

const showMembers = {
    'collaborator': true,
    'maintainer': true,
    'contributor': true
};
const members = computed(() => props.mod.members.filter(member => member.accepted && (showMembers[member.level] ?? false)));
const i18n = useI18n();
const locale = computed(() => i18n.locale.value);

const likes = computed(() => friendlyNumber(locale.value, props.mod.likes));
const downloads = computed(() => friendlyNumber(locale.value, props.mod.downloads));
const views = computed(() => friendlyNumber(locale.value, props.mod.views));

//If the user set their own donation, show that.
const ownerDonation = computed(() => props.mod.user.donation_url || props.mod.donation);

const tagLink = computed(() => `/g/${props.mod?.game?.short_name}/mods`);

onMounted(() => {
    if (process.client) {
        window['nitroAds'].createAd('mws-ads-mod-pane', {
            "refreshLimit": 20,
            "refreshTime": 60,
            "renderVisibleOnly": false,
            "refreshVisibleOnly": true,
            "sizes": [
                [
                "336",
                "280"
                ]
            ],
            "report": {
                "enabled": true,
                "icon": true,
                "wording": "Report Ad",
                "position": "bottom-right"
            },
        });
    }
});
</script>

<style>
@media (min-width:650px) and (max-width:1280px) {
    .mod-info.mod-thumbnail img {
        width: 300px;
    }

    .mod-info {
        padding: 1rem;
        flex-direction: row;
        gap: 1px;
    }
}
</style>