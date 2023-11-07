<template>
    <flex class="items-center text-xl" style="gap: 4rem; max-width: 1000px;" column>
        <a-img is-asset height="256" src="mws_logo_white.svg"/>
        <flex column>
            <b class="text-4xl mx-auto">{{ $t('about_mws') }}</b>
            <a-markdown :text="$t('about_mws_desc')"/>
        </flex>
        <flex column>
            <b class="text-4xl mx-auto">{{ $t('about_mws_values') }}</b>
            <a-markdown :text="$t('about_mws_values_desc')"/>
        </flex>
        <flex column>
            <b class="text-4xl mx-auto">{{ $t('our_team') }}</b>
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
import type { User } from '~/types/models';

const { data: management } = await useFetchMany<User>('users', { params: { role_ids: [3] } });
const { data: moderators } = await useFetchMany<User>('users', { params: { role_ids: [4] } });
</script>

<style>
.team-user {
    width: 130px !important;
}
</style>