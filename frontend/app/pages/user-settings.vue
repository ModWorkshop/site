<template>
	<page-block size="md">
		<Title>{{ $t('user_settings') }}</Title>
		<m-nav side :root="isMe ? `/user-settings` : `/user/${user.id}/edit`">
			<m-nav-link to="profile" alias="" :title="$t('profile')"/>
			<m-nav-link to="account" :title="$t('account_tab')"/>
			<m-nav-link v-if="isMe" to="settings" :title="$t('settings')"/>
			<m-nav-link v-if="isMe" to="content" :title="$t('content_tab')"/>
			<m-nav-link v-if="isMe" to="following" :title="$t('following')"/>
			<m-nav-link v-if="isMe" to="blocking" :title="$t('blocking')"/>
			<m-nav-link v-if="isMe" to="accounts" :title="$t('connected_accounts_tab')"/>
			<!-- <m-nav-link to="api" :title="$t('api_access_tab')"/> -->
			<template #content>
				<NuxtPage :user="user"/>
			</template>
		</m-nav>
	</page-block>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';
import { useStore } from '../store';
import type { User } from '../types/models';

definePageMeta({
	middleware: 'users-only'
});

const { user: me } = useStore();
const route = useRoute();
const isMe = !route.params.user;

provide('isMe', isMe);

const { data: user } = await useResource<User>('user', 'users', undefined, undefined, clone(me));
</script>
