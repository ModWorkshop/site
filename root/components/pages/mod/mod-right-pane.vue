<template>
    <flex column>
        <flex column gap="3" class="mod-info content-block self-start">
            <mod-thumbnail :thumbnail="mod.thumbnail" prefer-hq/>
            <flex column class="p-4 pt-0" gap="4">
                <flex class="text-lg" gap="2">
                    <span>
                        <a-icon icon="mdi:heart"/> {{likes}}
                    </span>
                    <span>
                        <a-icon icon="mdi:download"/> {{downloads}}
                    </span>
                    <span>
                        <a-icon icon="mdi:eye"/> {{views}}
                    </span>
                    <span v-if="mod.published_at" class="ml-auto">
                        <a-icon icon="mdi:calendar-import" :title="$t('published_at')"/> <time-ago :time="mod.published_at"/>
                    </span>
                    <span v-else-if="mod.created_at" class="ml-auto">
                        <a-icon icon="mdi:calendar-plus" :title="$t('upload_date')"/> <time-ago :time="mod.created_at"/>
                    </span>
                </flex>
    
                <flex v-if="mod.tags && mod.tags.length > 0" wrap>
                    <a-tag v-for="tag in mod.tags" :key="tag.id" :color="tag.color">
                        <NuxtLink class="no-hover" :to="`${tagLink}?selected-tags=${tag.id}`">{{tag.name}}</NuxtLink>
                    </a-tag>
                </flex>
    
                <flex class="colllaborators-block" column>
                    <flex>
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
        <div id="div-gpt-ad-mws-4" class="ad mt-1" style="max-height: 400px;"/>
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

const likes = computed(() => props.mod.likes);
const downloads = computed(() => props.mod.downloads);
const views = computed(() => props.mod.views);

//If the user set their own donation, show that.
const ownerDonation = computed(() => props.mod.user.donation_url || props.mod.donation);

const tagLink = computed(() => `/g/${props.mod?.game?.short_name}/mods`);
</script>