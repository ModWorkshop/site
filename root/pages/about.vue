<template>
    <flex class="items-center text-2xl" style="gap: 6rem; max-width: 1000px;" column>
        <a-img is-asset height="256" src="mws_logo_white.svg"/>
        <flex column>
            <b class="text-4xl self-center">About ModWorkshop</b>
            ModWorkshop is a platform for sharing and downloading mods for various games. Working together as a community to create tools, guides and more.
        </flex>
        <flex column>
            <b class="text-4xl self-center">Values</b>
            <span>
                <b>🔍 Open Source Modding</b> -
                Mods uploaded to the site are required to be open source (with rare exceptions) as stated in our <NuxtLink to="docs/rules">rules</NuxtLink>. So you know what you are installing.
                <a class="inline" href="https://modworkshop.net/thread/9025">There are plans to open source the site.</a>
            </span>
            <span>
                <b>😉 No Account Requirement</b> - Downloading mods should not require an account.
            </span>
            <span>
                <b>🚀 No Throttling</b> - You should be able to download mods at full speed without any artificial throttling.
            </span>
            <span>
                <b>🫂 Community Driven</b> - The site is open for the communtiy to contribute with moderators being volunteers helping in their free time.
            </span>
        </flex>
        <flex column gap="6">
            <b class="text-4xl self-center">Our Team</b>
            <flex column>
                <b>Management</b>
                <flex class="text-base" wrap gap="3">
                    <a-user v-for="user of management?.data" :key="user.id" class="team-user" :user="user" avatar-size="xl" :tag="false" title column/>
                </flex>
            </flex>
            <flex column>
                <b>Moderators</b>
                <flex class="text-base" wrap gap="3">
                    <a-user v-for="user of moderators?.data" :key="user.id" class="team-user" :user="user" avatar-size="xl" :tag="false" title column/>
                </flex>
            </flex>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { User } from '~/types/models';

const { data: management } = await useFetchMany<User>('users', { params: { role_ids: [3] } });
const { data: moderators } = await useFetchMany<User>('users', { params: { role_ids: [4] } });
</script>

<style>
.team-user {
    width: 130px !important;
}
</style>