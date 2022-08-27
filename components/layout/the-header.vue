<template>
    <client-only>
        <va-modal v-model="showNotifications" size="large" background-color="#2b3036">
            <template #content="{ ok }">
                <va-inner-loading v-if="!notifications" loading class="mb-4 mt-2"/>
                <template v-else>
                    <h2>{{$t('notifications')}}</h2>
                    <flex column class="overflow-y-scroll">
                        <a-notification v-for="notif of notifications.data" :key="notif.id" :notification="notif" :ok="ok" :notifications="notifications"/>
                    </flex>
                    <div class="mt-4">
                        <a-button icon="eye" to="/notifications">{{$t('browse_all_notifications')}}</a-button>
                    </div>
                </template>
            </template>
        </va-modal>
    </client-only>
    <header class="nav">
        <NuxtLink to="/">
            <img :src="logo" width="36">
        </NuxtLink>
        <flex gap="4" class="ml-3">
            <NuxtLink to="/upload">{{$t('upload_mod')}}</NuxtLink>
            <NuxtLink to="/games">{{$t('games')}}</NuxtLink>
            <NuxtLink to="/forum?category=news">{{$t('news')}}</NuxtLink>
            <NuxtLink to="https://discord.gg/Eear4JW">{{$t('discord')}}</NuxtLink>
            <NuxtLink to="/forum">{{$t('forum')}}</NuxtLink>
            <NuxtLink to="/support">{{$t('support_us')}}</NuxtLink>
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

    .md-editor-open header {
        z-index: 0;
    }
</style>