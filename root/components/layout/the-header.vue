<template>
    <a-modal v-model="showNotifications" size="md">
        <a-loading v-if="!notifications"/>
        <template v-else>
            <h2>{{$t('notifications')}}</h2>
            <flex column :class="{'overflow-y-scroll': notifications.data.length, 'p-2': true}" style="min-height: 150px;">
                <template v-if="notifications.data.length">
                    <a-notification v-for="notif of notifications.data" :key="notif.id" :notification="notif" :ok="() => showNotifications = false" :notifications="notifications"/>
                </template>
                <span v-else>
                    {{$t('no_alerts_found')}}
                </span>
            </flex>
            <flex class="mt-4">
                <a-button to="/notifications" @click="showNotifications = false"><i-mdi-eye/> {{$t('browse_all_notifications')}}</a-button>
                <a-button @click="markAsRead"><i-mdi-clock/> {{$t('mark_all_notifications')}}</a-button>
            </flex>
        </template>
    </a-modal>
    

    <header class="navbar">
        <NuxtLink to="/">
            <a-img alt="logo" :src="logo" width="36" height="36" is-asset/>
        </NuxtLink>
        <a-link-button class="ml-auto text-4xl sm:hidden" @click="headerClosed = !headerClosed"><i-mdi-menu/></a-link-button>
        <Transition name="left-slide">
            <flex v-show="!headerClosed" class="header-content">
                <flex id="header-buttons" gap="4" class="ml-3 sm:items-center">
                    <a-link-button v-if="!user?.ban" :to="user ? '/upload' : '/login'">{{$t('upload_mod')}}</a-link-button>
                    <a-link-button to="/games">{{$t('games')}}</a-link-button>
                    <a-link-button v-if="settings?.news_forum_category" class="max-lg:hidden" :to="`/forum?category=${settings?.news_forum_category}`">{{$t('news')}}</a-link-button>
                    <a-link-button class="max-lg:hidden" to="https://discord.gg/Eear4JW">{{$t('discord')}}</a-link-button>
                    <a-link-button class="max-lg:hidden" to="/forum">{{$t('forum')}}</a-link-button>
                    <a-link-button class="max-lg:hidden" to="/document/rules">{{$t('rules')}}</a-link-button>
                    <a-link-button class="max-lg:hidden" to="https://wiki.modworkshop.net/">{{$t('wiki')}}</a-link-button>
                    <a-link-button class="max-lg:hidden" to="/support">{{$t('support_us')}}</a-link-button>
                    <VDropdown class="hidden max-lg:block max-sm:hidden">
                        <a-link-button><i-mdi-dots-vertical/></a-link-button>
                        <template #popper>
                            <a-dropdown-item to="/forum?category=news">{{$t('news')}}</a-dropdown-item>
                            <a-dropdown-item to="https://discord.gg/Eear4JW">{{$t('discord')}}</a-dropdown-item>
                            <a-dropdown-item to="/forum">{{$t('forum')}}</a-dropdown-item>
                            <a-dropdown-item to="/rules">{{$t('rules')}}</a-dropdown-item>
                            <a-dropdown-item to="https://wiki.modworkshop.net/">{{$t('wiki')}}</a-dropdown-item>
                            <a-dropdown-item to="/support">{{$t('support_us')}}</a-dropdown-item>
                        </template>
                    </VDropdown>
                </flex>
                <flex id="user-items" class="sm:ml-auto mb-4 md:mb-0 md:mr-2" gap="4"> 
                    <VDropdown :shown="showSearch" no-auto-focus :triggers="[]" :auto-hide="false" popper-class="popper-big">
                        <div>
                            <a-input 
                                id="header-search"
                                v-model="query"
                                v-on-click-outside="() => showSearch = false"
                                class="search"
                                :placeholder="$t('search')"
                                maxlength="150"
                                @click="showSearch = true"
                                @keydown="onKeydownSearch"
                                @keyup.up.self="setSelectedSearch(-1)"
                                @keyup.down.self="setSelectedSearch(1)"
                                @keyup.enter="clickSelectedSearch"
                            />
                            <a-button :aria-label="$t('search')" @click="clickSelectedSearch">
                                <i-mdi-magnify/>
                            </a-button>
                        </div>
                        <template #popper>
                            <ClientOnly>
                                <h3>{{$t('search')}}</h3>
                                <flex column gap="1">
                                    <a-button
                                        v-for="[i, button] in searchButtons.entries()"
                                        :key="button.text"
                                        :to="`${button.to}?query=${query}`"
                                        :color="selectedSearch == i ? 'primary' : 'subtle'"
                                        class="search-button"
                                    >
                                        {{$t(button.text, [query, currentGame?.name])}}
                                    </a-button>
                                </flex>
                                <template v-if="query && mods && mods.data.length">
                                    <h3>{{$t('mods')}}</h3>
                                    <flex column gap="1">
                                        <template v-if="mods">
                                            <list-mod v-for="mod of mods.data" :key="mod.id" lite :mod="mod"/>
                                        </template>
                                    </flex>
                                </template>
                            </ClientOnly>
                        </template>
                    </VDropdown>
                    <flex v-if="user" class="items-center" gap="3">
                        <flex v-if="user.ban" column>
                            <span class="text-danger">
                                <i-mdi-alert/> {{$t('banned')}}
                            </span>
                            <span>
                                {{$t('expires')}}: <time-ago :time="user.ban.expire_date"/>
                            </span>
                        </flex>
                        <flex class="text-lg" gap="4">
                            <NuxtLink v-if="canSeeReports" :title="$t('reports')" :class="{'text-warning': hasReports, 'text-body': !hasReports}" to="/admin/reports">
                                <i-mdi-alert-box/> {{reportCount}}
                            </NuxtLink>
                            <NuxtLink v-if="canSeeWaiting" :title="$t('approvals')" :class="{'text-warning': hasWaiting, 'text-body': !hasWaiting}" to="/admin/approvals">
                                <i-mdi-clock/> {{waitingCount}}
                            </NuxtLink>
                            <span class="cursor-pointer" @click="showNotifications = true"><i-mdi-bell/> {{notificationCount}}</span>
                        </flex>
                        <VDropdown class="-order-1 md:order-1">
                            <a-user class="cursor-pointer" :user="user" :tag="false" no-color static/>
                            <template #popper>
                                <a-dropdown-item :to="userLink"><i-mdi-user/> {{$t('profile')}}</a-dropdown-item>
                                <a-dropdown-item to="/user-settings"><i-mdi-account-settings-variant/> {{$t('user_settings')}}</a-dropdown-item>
                                <a-dropdown-item to="/user-settings/content"><i-mdi-eye/> {{$t('content_settings')}}</a-dropdown-item>
                                <a-dropdown-item v-if="canSeeAdminPage" to="/admin"><i-mdi-cog/> {{$t('admin_page')}}</a-dropdown-item>
                                <div class="dropdown-splitter"/>
                                <a-dropdown-item to="/followed-mods"><i-mdi-plus/> {{$t('followed_mods')}}</a-dropdown-item>
                                <a-dropdown-item to="/liked-mods"><i-mdi-heart/> {{$t('liked_mods')}}</a-dropdown-item>
                                <div class="dropdown-splitter"/>
                                <a-dropdown-item @click="store.logout"><i-mdi-logout/> {{$t('logout')}}</a-dropdown-item>
                                <div class="dropdown-splitter"/>
                                <a-dropdown-item @click="store.toggleTheme">
                                    <i-mdi-white-balance-sunny v-if="store.theme == 'light'"/>
                                    <i-mdi-weather-night v-else/>
                                    {{$t(store.theme === 'light' ? 'light_theme' : 'dark_theme')}}
                                </a-dropdown-item>
                            </template>
                        </VDropdown>
                    </flex>
                    <flex v-else class="my-auto" gap="4">
                        <a-link-button to="/login">{{$t('login')}}</a-link-button>
                        <a-link-button to="/register">{{$t('register')}}</a-link-button>
                        <a-link-button @click="store.toggleTheme">
                            <i-mdi-white-balance-sunny v-if="store.theme === 'light'"/>
                            <i-mdi-weather-night v-else/>
                        </a-link-button>
                    </flex>
                </flex>
            </flex>
        </Transition>
    </header>
    <div v-if="!headerClosed" class="header-closer" @click.prevent="headerClosed = true"/>
</template>
<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { adminPagePerms } from '~~/utils/helpers';
import { useStore } from '../../store';
import { vOnClickOutside } from '@vueuse/components';
import { Mod } from '../../types/models';

const headerClosed = ref(true);

const store = useStore();
const settings = computed(() => store.settings);

const router = useRouter();

const { 
    user,
    notifications,
    notificationCount,
    currentGame,
    reportCount,
    waitingCount,
} = storeToRefs(store);
const query = ref('');
const showNotifications = ref(false);
const showSearch = ref(false);
const selectedSearch = ref(0);
const unlockedOwO = useState('unlockedOwO');
const searchBus = useEventBus<string>('search');

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
        { to: `/search/threads`, text: searching ? 'search_threads_matching' : 'threads' },
        { to: `/search/users`, text: searching ? 'search_users_matching' : 'users' },
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
    sort: 'views',
    query: query,
}, { onChange: () => query.value.length > 0, immediate: false });

watch(showNotifications, async () => {
    if (!notifications.value) {
        await store.getNotifications(1, 20);
    }
});

function onKeydownSearch() {
    if (query.value) {
        showSearch.value = true;
    }
}

watch(query, val => {
    unlockedOwO.value = val.toLowerCase() === 'owo';
    searchBus.emit(val);
});

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
    padding: 0.5rem;
    min-width: 200px;
}

header {
    top: 0;
    left: 0;
    z-index: 100;
    position: sticky;
    padding: 0.75rem;
    display: flex;
    grid-area: header;
    transition: left 0.25s;
}
</style>
<style>
.header-content {
    flex-grow: 1;
}

@media (min-width:640px) {
    .header-content {
        display: flex !important;
    }
}

@media (max-width:640px) {
    .header-content {
        flex-direction: column-reverse;
        justify-content: flex-end;
        position: absolute !important;
        padding: 1.5rem !important;
        left: 4px;
        top: 64px;
        min-width: 270px;
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
    }
}

.search .input {
    border-right: none;
}
.user {
    display: flex;
    gap: 0.25rem;
    align-items: center;
}

.user:hover {
    color: var(--text-color);
}

.header-items {
    display: flex;
    gap: 0.75rem;
}

.navbar {
    background-color: var(--header-footer-color);
}

.md-editor-open header {
    z-index: 0;
}


.left-slide-move, /* apply transition to moving elements */
.left-slide-enter-active,
.left-slide-leave-active {
  transition: all 0.25s ease;
}

.left-slide-enter-from,
.left-slide-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}

.popper-big .v-popper__inner {
    max-height: 64vh;
    padding: 1rem;
}
</style>