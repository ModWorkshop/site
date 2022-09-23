<template>
    <Html :class="lightTheme ? 'light' : 'dark'"/>
    <a-modal v-model="showNotifications" size="md">
        <a-loading v-if="!notifications"/>
        <template v-else>
            <h2>{{$t('notifications')}}</h2>
            <flex column class="overflow-y-scroll">
                <a-notification v-for="notif of notifications.data" :key="notif.id" :notification="notif" :ok="() => showNotifications = false" :notifications="notifications"/>
            </flex>
            <div class="mt-4">
                <a-button icon="eye" to="/notifications" @click="showNotifications = false">{{$t('browse_all_notifications')}}</a-button>
            </div>
        </template>
    </a-modal>
    <header class="nav">
        <NuxtLink to="/">
            <img :src="logo" width="36">
        </NuxtLink>
        <flex gap="4" class="ml-3">
            <a-link-button v-if="!user || !user.last_ban" to="/upload">{{$t('upload_mod')}}</a-link-button>
            <a-link-button to="/games">{{$t('games')}}</a-link-button>
            <a-link-button to="/forum?category=news">{{$t('news')}}</a-link-button>
            <a-link-button to="https://discord.gg/Eear4JW">{{$t('discord')}}</a-link-button>
            <a-link-button to="/forum">{{$t('forum')}}</a-link-button>
            <a-link-button to="https://wiki.modworkshop.net/">{{$t('wiki')}}</a-link-button>
            <a-link-button to="/support">{{$t('support_us')}}</a-link-button>
        </flex>
        <flex class="user-items mr-2 items-center" gap="4"> 
            <Popper offset-distance="8" :show="showSearch">
                <div>
                    <a-input 
                        v-model="search"
                        v-click-outside="() => showSearch = false"
                        class="search"
                        placeholder="Search"
                        style="width: 250px;"
                        maxlength="150"
                        @click="showSearch = true"
                        @keyup.up.self="setSelectedSearch(-1)"
                        @keyup.down.self="setSelectedSearch(1)"
                        @keyup.enter="clickSelectedSearch"
                    />
                    <a-button icon="search" style="padding: 0.6rem 0.75rem;"/>
                </div>
                <template #content>
                    <flex class="popper-big" column>
                        <a-button
                            v-for="[i, button] in searchButtons.entries()"
                            :key="button.text"
                            :to="`${button.to}?query=${search}`"
                            :color="selectedSearch == i ? 'primary' : 'subtle'"
                            class="search-button">
                            {{$t(button.text, [search])}}
                        </a-button>
                    </flex>
                </template>
            </Popper>
            <template v-if="user">
                <flex v-if="user.last_ban" column>
                    <span class="text-danger">
                        <font-awesome-icon icon="triangle-exclamation"/> Banned
                    </span>
                    <span>
                        Expires: <time-ago :time="user.last_ban.expire_date"/>
                    </span>
                </flex>
                <flex class="text-lg" gap="4">
                    <span class="cursor-pointer" @click="showNotifications = true"><font-awesome-icon icon="bell"/> {{notificationCount}}</span>
                    <!-- <span><font-awesome-icon icon="message"/> 0</span> -->
                </flex>
                <Popper arrow>
                    <a-user class="cursor-pointer" :user="user" :tag="false" :color="false" static/>
                    <template #content>
                        <a-dropdown-item icon="user" :to="`/user/${user.id}`">{{$t('profile')}}</a-dropdown-item>
                        <a-dropdown-item icon="cog" to="/user-settings">{{$t('user_settings')}}</a-dropdown-item>
                        <a-dropdown-item icon="eye" to="/user-settings?tab=content">{{$t('content_settings')}}</a-dropdown-item>
                        <a-dropdown-item v-if="isAdmin" icon="users-gear" to="/admin">{{$t('admin')}}</a-dropdown-item>
                        <a-dropdown-item icon="arrow-right-from-bracket" @click="store.logout">{{$t('logout')}}</a-dropdown-item>
                        <div class="dropdown-splitter"/>
                        <a-dropdown-item :icon="lightTheme ? 'moon' : 'sun'" @click="toggleTheme">
                            {{$t(lightTheme ? 'dark_theme' : 'light_theme')}}
                        </a-dropdown-item>
                        <a-dropdown-item icon="globe">English</a-dropdown-item>
                    </template>
                </Popper>
            </template>
            <a-loading v-else-if="userIsLoading"/>
            <flex v-else class="my-auto" gap="2">
                <NuxtLink to="/login">{{$t('login')}}</NuxtLink>
                <NuxtLink to="/register">{{$t('register')}}</NuxtLink>
            </flex>
        </flex>
    </header>
</template>
<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useStore } from '../../store';

const store = useStore();
const router = useRouter();
const { user, notifications, userIsLoading, notificationCount, lightTheme } = storeToRefs(store);
const search = ref('');
const showNotifications = ref(false);
const showSearch = ref(false);
const selectedSearch = ref(1);
const savedTheme = useCookie('theme');

const computedlightTheme = computed(() => {
    if (savedTheme.value) {
        return savedTheme.value === 'light';
    } else {
        return false;
    }
});

watch(computedlightTheme, () => lightTheme.value = computedlightTheme.value, { immediate: true });

const logo = computed(() => lightTheme.value ? '/mws_logo_black.svg' : '/mws_logo_white.svg');

function toggleTheme() {
    if (!savedTheme.value || savedTheme.value == 'dark') {
        savedTheme.value = 'light';
    } else if (savedTheme.value == 'light') {
        savedTheme.value = null;
    }
}

const searchButtons = computed(() => {
    const buttons = [
        { to: `/search/mods`, text: 'search_mods' },
        { to: `/search/threads`, text: 'search_threads' },
        { to: `/search/users`, text: 'search_users' },
    ];
    if (store.currentGame) {
        buttons.unshift({ to: `/g/${store.currentGame.short_name}/forum`, text: 'search_threads_game' });
        buttons.unshift({ to: `/g/${store.currentGame.short_name}`, text: 'search_mods_game' });
    }
    return buttons;
});

const isAdmin = computed(() => store.hasPermission('admin'));

watch(showNotifications, async () => {
    if (!notifications.value) {
        await store.getNotifications(1, 20);
    }
});

watch(search, val => {
    if (val) {
        showSearch.value = true;
    }
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

function clickSelectedSearch(e) {
    router.push((searchButtons[selectedSearch.value]?.to ?? '/search/mods') + `?query=${search.value}`);
}
</script>
<style scoped>
.selected-search {
    background-color: var(--primary-color);
}

.search-button {
    padding: 1rem;
}
</style>
<style>
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

.user-items {
    margin-left: auto;
}

.header-items {
    display: flex;
    gap: 0.75rem;
}

header {
    top: 0;
    z-index: 100;
    align-items: center;
    position: sticky;
    padding: 0.75rem;
    display: flex;
    background-color: var(--header-footer-color);
    grid-area: header;
}

.md-editor-open header {
    z-index: 0;
}
</style>