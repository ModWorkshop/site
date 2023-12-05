<template>
    <m-flex column class="self-start mod-info-holder" gap="3">
        <mod-thumbnail :thumbnail="mod.thumbnail" prefer-hq/>

        <m-flex class="content-block p-4" gap="2" column>
            <mod-buttons :mod="mod" :static="static"/>
        </m-flex>

        <m-flex column gap="3" class="mod-info content-block p-4">
            <m-flex>
                <span class="text-secondary"> <i-mdi-download/> {{ $t('downloads') }}</span>
                <span class="ml-auto">{{downloads}}</span>
            </m-flex>

            <m-flex>
                <span class="text-secondary"> <i-mdi-eye/> {{ $t('views') }}</span>
                <span class="ml-auto">{{views}}</span>
            </m-flex>

            <m-flex v-if="mod.published_at">
                <span class="text-secondary"> <i-mdi-calendar-import/> {{ $t('published_at') }}</span>
                <m-time-ago :time="mod.published_at" class="ml-auto"/>
            </m-flex>
            <m-flex v-else-if="mod.created_at">
                <span class="text-secondary"> <i-mdi-calendar-plus/> {{ $t('upload_date') }}</span>
                <m-time-ago :time="mod.created_at" class="ml-auto"/>
            </m-flex>

            <m-flex class="items-center" gap="0">
                <span :title="$t('last_updated')" class="text-secondary">
                    <i-mdi-clock/> {{ $t('last_updated') }}
                </span>
                <span v-if="mod.bumped_at" class="ml-auto">
                    <m-time-ago v-if="!mod.last_user" :time="mod.bumped_at"/>
                    <span v-else class="items-center inline-flex gap-1">
                        <i18n-t keypath="by_user_time_ago" scope="global">
                            <template #user>
                                <a-user avatar-size="xs" :user="mod.last_user" :tag="false" :avatar="false"/>
                            </template>
                            <template #time>
                                <m-time-ago :time="mod.bumped_at"/>
                            </template>
                        </i18n-t>
                    </span>
                </span>
            </m-flex>

            <m-flex v-if="mod.version" :title="$t('version')">
                <span class="text-secondary"><i-mdi-tag/> {{$t('version')}} </span>
                <span class="ml-auto">{{mod.version}}</span>
            </m-flex>

            <m-flex gap="2" column>
                <span class="text-secondary">
                    <i-mdi-account-group/> {{ $t('members_tab') }}
                </span>
                <m-flex class="colllaborators-block" column>
                    <m-flex wrap>
                        <a-user :user="mod.user" :details="$t('owner')"/>
                        <donation-button v-if="ownerDonation" class="ml-auto" :link="ownerDonation"/>
                    </m-flex>
    
                    <m-flex v-for="member of members" :key="member.id">
                        <a-user :user="member" :details="$t(`member_level_${member.level}`)"/>
                        <donation-button v-if="member.donation_url" class="ml-auto" :link="member.donation_url"/>
                    </m-flex>
                </m-flex>
            </m-flex>
            
            <m-flex v-if="mod.tags && mod.tags.length > 0" column gap="2">
                <span class="text-secondary">
                    <i-mdi-tag-multiple/> {{ $t('tags') }}
                </span>
                <m-flex class="items-center" wrap>
                    <m-tag v-for="tag in mod.tags" :key="tag.id" :color="tag.color">
                        <NuxtLink class="no-hover" :to="`${tagLink}?selected-tags=${tag.id}`">{{tag.name}}</NuxtLink>
                    </m-tag>
                </m-flex>
            </m-flex>
        </m-flex>
        <div id="mws-ads-mod-pane" class="ad mt-1"/>
    </m-flex>
</template>

<script setup lang="ts">
import type { Mod } from '~~/types/models';
const props = defineProps<{
    mod: Mod,
    static?: boolean
}>();

const showMembers = {
    'collaborator': true,
    'maintainer': true,
    'contributor': true
};
const members = computed(() => props.mod.members.filter(member => member.accepted && (showMembers[member.level] ?? false)));
const i18n = useI18n();
const locale = computed(() => i18n.locale.value);

const downloads = computed(() => friendlyNumber(locale.value, props.mod.downloads));
const views = computed(() => friendlyNumber(locale.value, props.mod.views));

//If the user set their own donation, show that.
const ownerDonation = computed(() => props.mod.user?.donation_url || props.mod.donation);

const tagLink = computed(() => `/g/${props.mod?.game?.short_name}/mods`);

onMounted(() => {
    if (process.client) {
        window['nitroAds'].createAd('mws-ads-mod-pane', {
            "refreshLimit": 0,
            "refreshTime": 30,
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
        padding: 1.5rem;
    }
}
</style>