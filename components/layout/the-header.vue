<template>
    <header>
        <NuxtLink to="/">
            <img :src="logo" width="38"/>
        </NuxtLink>
        <flex gap="2" class="ml-3">
            <NuxtLink to="/upload">Upload a Mod</NuxtLink>
            <NuxtLink to="/games">Games</NuxtLink>
            <a href="https://discord.gg/Eear4JW">Discord</a>
            <NuxtLink to="/support">Support Us</NuxtLink>
            <a-dropdown>
                <a href="#">More</a>
                <template #items>
                    <a-dropdown-item>Rules</a-dropdown-item>
                    <a-dropdown-item>About Us</a-dropdown-item>
                    <a-dropdown-item>Steam Group</a-dropdown-item>
                </template>
            </a-dropdown>
        </flex>
        <div class="user-items mr-3"> 
            <Popper v-if="user" arrow>
                <flex gap="1">
                    <a-avatar class="cursor-pointer" :src="user.avatar"/>
                    <a class="user cursor-pointer">
                        <span>{{user.name}}</span>
                    </a>
                </flex>
                <template #content>
                    <a-dropdown-item :to="`/user/${user.id}`">Profile</a-dropdown-item>
                    <a-dropdown-item>Liked Mods</a-dropdown-item>
                    <a-dropdown-item>Followed Mods</a-dropdown-item>
                    <div class="dropdown-splitter"/>
                    <a-dropdown-item to="/user-settings">User Settings</a-dropdown-item>
                    <a-dropdown-item>ModCP</a-dropdown-item>
                    <a-dropdown-item @click="logout">Log Out</a-dropdown-item>
                </template>
            </Popper>
            <flex gap="2" v-else>
                <NuxtLink to="/login">Login</NuxtLink>
                <NuxtLink to="/register">Register</NuxtLink>
            </flex>
        </div>
    </header>
</template>
<script setup>
import { useStore } from '../../store';
const logo = computed(() => '/mws_logo_white.svg'); //TODO: redo color mode

const store = useStore();
const user = computed(() => store.user);
const { $ftch } = useNuxtApp();

async function logout() {
    await $ftch('/logout', { method: 'POST' });
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
        gap: 0.75rem;
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