<template>
	<m-modal v-model="showNotifications" size="md" :title="$t('notifications')">
		<m-loading v-if="!notifications"/>
		<template v-else>
			<m-flex column :class="{ 'overflow-y-scroll': notifications.data.length, 'p-2': true }" style="min-height: 150px;">
				<template v-if="notifications.data.length">
					<list-notification v-for="notif of notifications.data" :key="notif.id" :notification="notif" :ok="() => showNotifications = false" :notifications="notifications"/>
				</template>
				<span v-else>
					{{ $t('no_alerts_found') }}
				</span>
			</m-flex>
			<m-flex class="mt-4" wrap>
				<m-button to="/notifications" @click="showNotifications = false"><i-mdi-eye/> {{ $t('browse_all_notifications') }}</m-button>
				<m-button @click="markAsRead"><i-mdi-clock/> {{ $t('mark_all_notifications') }}</m-button>
			</m-flex>
		</template>
	</m-modal>

	<header class="navbar content-block">
		<NuxtLink to="/">
			<m-img alt="logo" :src="logo" width="36" height="36" is-asset/>
		</NuxtLink>
		<m-link class="ml-auto text-4xl md:hidden" @click="headerClosed = !headerClosed"><i-mdi-menu/></m-link>
		<Transition name="left-slide">
			<m-flex v-show="!headerClosed" class="header-content">
				<m-flex id="header-buttons" gap="4" class="ml-4">
					<m-link v-if="!user?.ban" :to="user ? '/upload' : '/login'">{{ $t('upload_mod') }}</m-link>
					<m-link to="/mods">{{ $t('mods') }}</m-link>
					<m-link class="max-lg:hidden" to="/games">{{ $t('games') }}</m-link>
					<m-link v-if="settings?.news_forum_category" class="max-xl:hidden" :to="`/forum?category=${settings?.news_forum_category}`">{{ $t('news') }}</m-link>
					<m-link class="max-lg:hidden" to="https://discord.gg/Eear4JW">{{ $t('discord') }}</m-link>
					<m-link class="max-2xl:hidden" to="/forum">{{ $t('forum') }}</m-link>
					<m-link class="max-xl:hidden" to="/support">{{ $t('support_us') }}</m-link>
					<m-link class="max-lg:hidden" to="/document/rules">{{ $t('rules') }}</m-link>
					<m-dropdown class="max-md:hidden">
						<m-link>{{ $t('more') }} <i-mdi-chevron-down/></m-link>
						<template #content>
							<m-dropdown-item to="https://wiki.modworkshop.net/">{{ $t('wiki') }}</m-dropdown-item>
							<m-dropdown-item to="https://translate.modworkshop.net/">{{ $t('translation_site') }}</m-dropdown-item>
							<m-dropdown-item class="lg:!hidden" to="https://discord.gg/Eear4JW">{{ $t('discord') }}</m-dropdown-item>
							<m-dropdown-item to="/document/rules" class="lg:!hidden">{{ $t('rules') }}</m-dropdown-item>
							<m-dropdown-item to="/forum?category=news" class="xl:!hidden">{{ $t('news') }}</m-dropdown-item>
							<m-dropdown-item to="/forum" class="2xl:!hidden">{{ $t('forum') }}</m-dropdown-item>
							<m-dropdown-item to="/support" class="xl:!hidden">{{ $t('support_us') }}</m-dropdown-item>
						</template>
					</m-dropdown>

					<m-link class="max-sm:block hidden" to="https://wiki.modworkshop.net/">{{ $t('wiki') }}</m-link>
					<m-link class="max-sm:block hidden" to="https://translate.modworkshop.net/">{{ $t('translation_site') }}</m-link>
				</m-flex>
				<m-flex id="user-items" class="md:ml-auto mb-4 md:mb-0 md:mr-2 items-center" gap="4">
					<the-header-search/>
					<m-flex v-if="user" class="items-center" gap="4">
						<m-flex v-if="user.ban" column>
							<span class="text-danger">
								<i-mdi-alert/> {{ $t('banned') }}
							</span>
							<span>
								{{ $t('expires') }}: <m-time :datetime="user.ban.expire_date" relative/>
							</span>
						</m-flex>
						<m-flex class="text-lg max-sm:ml-auto" gap="2">
							<NuxtLink v-if="canSeeReports" :title="$t('reports')" :class="{ 'text-warning': hasReports, 'text-body': !hasReports }" to="/admin/reports">
								<i-mdi-alert-box/> {{ reportCount }}
							</NuxtLink>
							<NuxtLink v-if="canSeeWaiting" :title="$t('approvals')" :class="{ 'text-warning': hasWaiting, 'text-body': !hasWaiting }" to="/admin/approvals">
								<i-mdi-clock/> {{ waitingCount }}
							</NuxtLink>
							<span class="cursor-pointer" @click="showNotifications = true"><i-mdi-bell/> {{ notificationCount }}</span>
						</m-flex>
						<m-dropdown class="-order-1 md:order-1" align="end" dropdown-class="user-dropdown">
							<m-flex class="items-center">
								<m-avatar class="cursor-pointer" :src="user.avatar" :use-thumb="user.avatar_has_thumb"/>
							</m-flex>
							<template #content>
								<a-user class="m-1" :user="user" :tag="false" static/>
								<div class="dropdown-splitter"/>
								<m-dropdown-item :to="userLink"><i-mdi-user/> {{ $t('profile') }}</m-dropdown-item>
								<m-dropdown-item to="/user-settings"><i-mdi-account-settings-variant/> {{ $t('user_settings') }}</m-dropdown-item>
								<m-dropdown-item to="/user-settings/content"><i-mdi-eye/> {{ $t('content_settings') }}</m-dropdown-item>
								<m-dropdown-item v-if="canSeeAdminPage" to="/admin"><i-mdi-cog/> {{ $t('admin_page') }}</m-dropdown-item>
								<m-dropdown-item to="/customize"><i-ri-brush-2-fill/> {{ $t('customize') }}</m-dropdown-item>
								<div class="dropdown-splitter"/>
								<m-dropdown-item to="/followed-mods"><i-mdi-plus/> {{ $t('followed_mods') }}</m-dropdown-item>
								<m-dropdown-item to="/liked-mods"><i-mdi-heart/> {{ $t('liked_mods') }}</m-dropdown-item>
								<div class="dropdown-splitter"/>
								<m-dropdown-item @click="store.logout"><i-mdi-logout/> {{ $t('logout') }}</m-dropdown-item>
							</template>
						</m-dropdown>
					</m-flex>
					<m-flex v-else class="my-auto" gap="4">
						<m-link to="/login">{{ $t('login') }}</m-link>
						<m-link to="/register">{{ $t('register') }}</m-link>
						<m-link to="/customize"><i-mdi-cog/></m-link>
					</m-flex>
				</m-flex>
			</m-flex>
		</Transition>
	</header>
	<div v-if="!headerClosed" class="header-closer" @click.prevent="headerClosed = true"/>
</template>
<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useStore } from '~/store';

const store = useStore();
const {
	user,
	notifications,
	notificationCount,
	reportCount,
	waitingCount
} = storeToRefs(store);

const headerClosed = ref(true);
const showNotifications = ref(false);
const settings = computed(() => store.settings);
const logo = computed(() => store.theme === 'light' ? 'mws_logo_black.svg' : 'mws_logo_white.svg');
const canSeeAdminPage = computed(() => adminPagePerms.some(perm => store.hasPermission(perm)));
const hasReports = computed(() => reportCount?.value && reportCount.value > 0);
const hasWaiting = computed(() => waitingCount?.value && waitingCount.value > 0);
const canSeeReports = computed(() => store.hasPermission('manage-users'));
const canSeeWaiting = computed(() => store.hasPermission('manage-mods'));

const userLink = computed(() => {
	if (user.value!.unique_name) {
		return `/user/${user.value!.unique_name}`;
	} else {
		return `/user/${user.value!.id}`;
	}
});

watch(showNotifications, async () => {
	if (!notifications.value) {
		await store.getNotifications(1, 20);
	}
});

async function markAsRead() {
	await markAllNotificationsAsRead(notifications.value?.data, notificationCount);
}
</script>

<style scoped>
.header-closer {
	background-color: #00000080;
	position: absolute;
	width: 100%;
	height: 100%;
	z-index: 10;
}

header {
	top: 0;
	left: 0;
	z-index: 100;
	position: sticky;
	padding: 1rem;
	display: flex;
	grid-area: header;
	transition: left 0.25s;
}

#header-buttons {
	align-items: center;
}

.header-content {
	flex-grow: 1;
}

.user {
	display: flex;
}

.navbar {
	background-color: var(--header-footer-color);
}

@media (min-width: 768px) {
	.header-content {
		display: flex !important;
	}
}

@media (max-width: 767px) {
	.header-content {
		flex-direction: column-reverse;
		justify-content: flex-end;
		position: absolute !important;
		padding: 1.5rem !important;
		left: 0px;
		top: 64px;
		min-width: 290px;
		height: 100vh;
		background-color: var(--header-footer-color);
	}

	.header-content .link-button {
		display: inline-block;
	}

	#user-items {
		flex-direction: column-reverse;
	}

	#header-buttons {
		flex-direction: column;
		align-items: start !important;
	}
}
</style>

<style>
.md-editor-open header {
	z-index: 0;
}

.left-slide-move, .left-slide-enter-active, .left-slide-leave-active {
	transition: all 0.25s ease;
}

.left-slide-enter-from, .left-slide-leave-to {
	opacity: 0;
	transform: translateX(-100%);
}

.user-dropdown {
	min-width: 200px;
}

@media (max-width: 767px) {
	.user-dropdown {
		min-width: 250px;
		margin-left: 1rem;
	}
}
</style>
