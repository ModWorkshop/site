<template>
    <va-modal v-model="showNotifications" size="large" background-color="#2b3036">
        <template #content="{ ok }">
            <va-inner-loading v-if="!notifications" loading class="mb-4 mt-2"/>
            <flex v-else column gap="4" class="h-full">
                <h2>Notifications</h2>
                <flex column class="overflow-y-scroll">
                    <a-notification v-for="notif of notifications.data" :key="notif.id" :notification="notif" :ok="ok"/>
                </flex>
                <div>
                    <a-button icon="eye" to="notifications">{{$t('browse_all_notifications')}}</a-button>
                </div>
            </flex>
        </template>
    </va-modal>
    <header class="nav">
        <NuxtLink to="/">
            <img :src="logo" width="36">
        </NuxtLink>
        <flex gap="4" class="ml-3">
            <NuxtLink to="/upload">Upload a Mod</NuxtLink>
            <NuxtLink to="/games">Games</NuxtLink>
            <NuxtLink to="/blog">Blog</NuxtLink>
            <a href="https://discord.gg/Eear4JW">Discord</a>
            <NuxtLink to="/forum">Forum</NuxtLink>
            <NuxtLink to="/support">Support Us</NuxtLink>
            <Popper arrow>
                <a href="#"><font-awesome-icon icon="ellipsis"/> </a>
                <template #content>
                    <a-dropdown-item>Rules</a-dropdown-item>
                    <a-dropdown-item to="/about">About Us</a-dropdown-item>
                    <a-dropdown-item>Steam Group</a-dropdown-item>
                </template>
            </Popper>
        </flex>
        <flex class="user-items mr-2" gap="6"> 
            <div>
                <a-input v-model="search" placeholder="Search" style="width: 250px;"/>
                <a-button icon="search" style="padding: 0.6rem 0.75rem;"/>
            </div>
            <template v-if="user">
                <flex class="my-auto text-lg" gap="4">
                    <span class="cursor-pointer" @click="showNotifications = true"><font-awesome-icon icon="bell"/> {{notificationCount}}</span>
                    <span><font-awesome-icon icon="message"/> 0</span>
                </flex>
                <Popper arrow>
                    <flex>
                        <a-avatar class="cursor-pointer" :src="user.avatar"/>
                        <a class="user cursor-pointer">
                            <span>{{user.name}}</span>
                        </a>
                    </flex>
                    <template #content>
                        <a-dropdown-item icon="user" :to="`/user/${user.id}`">Profile</a-dropdown-item>
                        <a-dropdown-item icon="heart">Liked Mods</a-dropdown-item>
                        <a-dropdown-item icon="plus">Followed Mods</a-dropdown-item>
                        <div class="dropdown-splitter"/>
                        <a-dropdown-item icon="cog" to="/user-settings">User Settings</a-dropdown-item>
                        <a-dropdown-item icon="users-gear" to="/admin">Admin</a-dropdown-item>
                        <a-dropdown-item icon="arrow-right-from-bracket" @click="logout">Log Out</a-dropdown-item>
                        <div class="dropdown-splitter"/>
                        <a-dropdown-item icon="globe">English</a-dropdown-item>
                    </template>
                </Popper>
            </template>
            <div v-else-if="userIsLoading" class="mr-2 my-auto">
                <va-inner-loading :loading="true"/>
            </div>
            <flex v-else class="my-auto" gap="2">
                <NuxtLink to="/login">{{$t('login')}}</NuxtLink>
                <NuxtLink to="/register">{{$t('register')}}</NuxtLink>
            </flex>
        </flex>
    </header>
</template>
<script setup>
import { storeToRefs } from 'pinia';
import { reloadToken } from '~~/utils/helpers';
import { useStore } from '../../store';
const logo = computed(() => '/mws_logo_white.svg'); //TODO: redo color mode

const store = useStore();
const { user, notifications, userIsLoading, notificationCount } = storeToRefs(store);
const search = ref('');
const showNotifications = ref(false);

watch(showNotifications, async () => {
    if (!notifications.value) {
        await store.getNotifications(1, 20);
    }
});

async function logout() {
    await usePost('/logout');
    reloadToken();
    user.value = null;
}
</script>
<style>
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

    .nav a {
        color: var(--text-color);
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
</style>