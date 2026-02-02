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
							<m-dropdown-item to="/games" class="lg:!hidden">{{ $t('games') }}</m-dropdown-item>
							<m-dropdown-item to="/forum?category=news" class="xl:!hidden">{{ $t('news') }}</m-dropdown-item>
							<m-dropdown-item to="/forum" class="2xl:!hidden">{{ $t('forum') }}</m-dropdown-item>
							<m-dropdown-item to="/support" class="xl:!hidden">{{ $t('support_us') }}</m-dropdown-item>
						</template>
					</m-dropdown>

					<m-link class="max-sm:block hidden" to="https://wiki.modworkshop.net/">{{ $t('wiki') }}</m-link>
					<m-link class="max-sm:block hidden" to="https://translate.modworkshop.net/">{{ $t('translation_site') }}</m-link>
				</m-flex>
				<m-flex id="user-items" class="md:ml-auto mb-4 md:mb-0 md:mr-2 items-center" gap="4">
					<m-dropdown v-model:open="showSearch" :close-on-click="false" :trap-focus="false" :auto-hide="false" align="end" dropdown-class="search-dropdown">
						<m-flex>
							<input
								v-if="showSearch"
								id="header-search"
								ref="searchInput"
								v-model="query"
								class="searchbox"
								inline
								:placeholder="$t('search')"
								@click.stop
								@keydown="onKeydownSearch"
								@keyup.up.self="setSelectedSearch(-1)"
								@keyup.down.self="setSelectedSearch(1)"
								@keyup.enter="clickSelectedSearch"
							>
							<m-flex v-else id="header-search" inline class="searchbox">
								<i-mdi-magnify/><span class="text-secondary my-auto">{{ $t('search') }}</span>
								<span class="ml-auto my-auto max-md:hidden">
									<kbd>CTRL</kbd> <kbd>K</kbd>
								</span>
							</m-flex>
						</m-flex>
						<template #content>
							<ClientOnly>
								<h3>{{ $t('search') }}</h3>
								<m-flex column gap="1">
									<m-button
										v-for="[i, button] in searchButtons.entries()"
										:key="button.text"
										:to="`${button.to}?query=${query}`"
										:color="selectedSearch == i ? 'primary' : 'subtle'"
										class="search-button"
									>
										{{ $t(button.text, [query, currentGame?.name]) }}
									</m-button>
								</m-flex>
								<template v-if="query && mods && mods.data.length">
									<h3>{{ $t('mods') }}</h3>
									<m-flex column gap="2">
										<template v-if="mods">
											<search-list-mod v-for="mod of mods.data" :key="mod.id" lite :mod="mod"/>
										</template>
									</m-flex>
								</template>
							</ClientOnly>
						</template>
					</m-dropdown>
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
import { vOnClickOutside } from '@vueuse/components';
import type { Mod } from '~/types/models';

const router = useRouter();

const store = useStore();
const {
	user,
	notifications,
	notificationCount,
	currentGame,
	reportCount,
	waitingCount
} = storeToRefs(store);

const headerClosed = ref(true);
const query = ref('');
const showNotifications = ref(false);
const showSearch = ref(false);
const selectedSearch = ref(0);
const searchInput = ref();
const unlockedOwO = useState('unlockedOwO');
const searchBus = useEventBus<string>('search');

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

const searchButtons = computed(() => {
	const searching = query.value.length > 0;
	const buttons = [
		{ to: `/search/mods`, text: searching ? 'search_mods_matching' : 'mods' },
		{ to: `/search/games`, text: searching ? 'search_games_matching' : 'games' },
		{ to: `/search/threads`, text: searching ? 'search_threads_matching' : 'threads' },
		{ to: `/search/users`, text: searching ? 'search_users_matching' : 'users' }
	];
	if (currentGame.value) {
		buttons.unshift({ to: `/g/${currentGame.value.short_name}/forum`, text: searching ? 'search_threads_game_matching' : 'search_threads_game' });
		buttons.unshift({ to: `/g/${currentGame.value.short_name}/mods`, text: searching ? 'search_mods_game_matching' : 'search_mods_game' });
	}
	return buttons;
});

// Simple mod searcher
const { data: mods } = await useWatchedFetchMany<Mod>(() => currentGame.value ? `games/${currentGame.value.id}/mods` : 'mods', {
	limit: 5,
	sort: 'best_match',
	query: query,
	cacheData: true
}, { onChange: () => query.value.length > 0, immediate: false });

watch(showNotifications, async () => {
	if (!notifications.value) {
		await store.getNotifications(1, 20);
	}
});

watch(searchInput, val => {
	if (val) {
		setTimeout(() => val.focus(), 100);
	}
});

watch(query, val => {
	unlockedOwO.value = val.toLowerCase() === 'owo';
	searchBus.emit(val);
});

onMounted(() => {
	window.addEventListener('keydown', function (e) {
		if (e.ctrlKey && e.key === 'k' /** k */) {
			showSearch.value = true;
			e.preventDefault();
		}

		if (e.key === 'Escape') {
			showSearch.value = false;
		}
	});
});

function onKeydownSearch() {
	if (query.value) {
		showSearch.value = true;
	}
}

function setSelectedSearch(direction: number) {
	selectedSearch.value += direction;

	if (selectedSearch.value >= searchButtons.value.length) {
		selectedSearch.value = 0;
	}

	if (selectedSearch.value < 0) {
		selectedSearch.value = searchButtons.value.length - 1;
	}
}

function clickSelectedSearch() {
	router.replace({
		path: searchButtons.value[selectedSearch.value]?.to ?? '/search/mods',
		query: { query: query.value }
	});
}

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
.selected-search {
	background-color: var(--primary-color);
}

.search-button {
	min-width: 250px;
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

kbd {
	vertical-align: middle;
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
#header-search {
	line-height: revert;
	width: 280px;
	height: 36px;
}

@media (max-width: 1024px) {
	#header-search {
		width: 200px;
	}
}

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

.search-dropdown {
	max-height: 64vh;
	padding: 1rem;
}

.searchbox:focus-visible {
	outline: none;
	border-color: var(--primary-color);
}

.searchbox {
	padding: 0.7rem;
	flex: 1;
	transition: border-color 0.25s;
	color: var(--text-color);
	background-color: var(--input-bg-color);
	border-radius: var(--border-radius);
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
