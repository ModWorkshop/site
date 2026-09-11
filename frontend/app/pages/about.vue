<template>
	<m-flex class="items-center text-xl" style="gap: 4rem; max-width: 1000px;" column>
		<m-img is-asset width="256" class="mt-10" :src="theme === 'dark' ? 'mws_logo_white.svg' : 'mws_logo_black.svg'"/>
		<m-flex column>
			<b class="text-4xl mx-auto">{{ $t('about_mws') }}</b>
			<md-content :text="$t('about_mws_desc')"/>
		</m-flex>
		<m-flex column>
			<b class="text-4xl mx-auto">{{ $t('about_mws_values') }}</b>
			<md-content :text="$t('about_mws_values_desc')"/>
		</m-flex>
		<m-flex column>
			<b class="text-4xl mx-auto">{{ $t('our_team') }}</b>
			<m-flex column>
				<b>Management</b>
				<m-flex class="text-base" wrap gap="3">
					<a-user v-for="user of management?.data" :key="user.id" class="team-user" :user="user" avatar-size="xl" :tag="false" title column/>
				</m-flex>
			</m-flex>
			<m-flex column>
				<b>Moderators</b>
				<m-flex class="text-base" wrap gap="3">
					<a-user v-for="user of moderators?.data" :key="user.id" class="team-user" :user="user" avatar-size="xl" :tag="false" title column/>
				</m-flex>
			</m-flex>
		</m-flex>
	</m-flex>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { User } from '~/types/models';

const { theme } = useStore();

const { data: management } = await useFetchMany<User>('users', { query: { role_ids: [3] } });
const { data: moderators } = await useFetchMany<User>('users', { query: { role_ids: [4] } });
</script>

<style>
.team-user {
	width: 130px !important;
}
</style>
