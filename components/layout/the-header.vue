<template>
    <header>
        <NuxtLink to="/">
            <img :src="logo" width="36"/>
        </NuxtLink>
        <flex gap="4" class="ml-3">
            <NuxtLink to="/upload">Upload a Mod</NuxtLink>
            <NuxtLink to="/games">Games</NuxtLink>
            <a href="https://discord.gg/Eear4JW">Discord</a>
            <NuxtLink to="/blog">Blog</NuxtLink>
            <NuxtLink to="/forum">Forum</NuxtLink>
            <NuxtLink to="/support">Support Us</NuxtLink>
            <Popper arrow>
                <a href="#"><font-awesome-icon icon="ellipsis"/> </a>
                <template #content>
                    <a-dropdown-item>Rules</a-dropdown-item>
                    <a-dropdown-item>About Us</a-dropdown-item>
                    <a-dropdown-item>Steam Group</a-dropdown-item>
                </template>
            </Popper>
        </flex>
        <flex class="user-items mr-2" gap="6"> 
            <div>
                <a-input placeholder="Search" style="width: 250px;" v-model="search"/>
                <a-button icon="search" style="padding: 0.6rem 0.75rem;"/>
            </div>
            <template v-if="user">
                <flex class="my-auto text-lg" gap="4">
                    <span><font-awesome-icon icon="bell"/> 0</span>
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
                        <a-dropdown-item icon="users-gear">ModCP</a-dropdown-item>
                        <a-dropdown-item icon="arrow-right-from-bracket" @click="logout">Log Out</a-dropdown-item>
                        <div class="dropdown-splitter"/>
                        <a-dropdown-item icon="globe">English</a-dropdown-item>
                    </template>
                </Popper>
            </template>
            <flex class="my-auto" gap="2" v-else>
                <NuxtLink to="/login">Login</NuxtLink>
                <NuxtLink to="/register">Register</NuxtLink>
            </flex>
        </flex>
    </header>
</template>
<script setup>
import { reloadToken } from '~~/utils/helpers';
import { useStore } from '../../store';
const logo = computed(() => '/mws_logo_white.svg'); //TODO: redo color mode

const store = useStore();
const user = computed(() => store.user);
const search = ref('');

async function logout() {
    await usePost('/logout');
    reloadToken();
    store.user = null;
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

    header a {
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