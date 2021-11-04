<template>
    <header>
        <NuxtLink to="/">
            <img :src="logo" width="38"/>
        </NuxtLink>
        <div class="ml-3 header-items">
            <NuxtLink to="/upload">Upload a Mod</NuxtLink>
            <NuxtLink to="/theme-test">Theme Test</NuxtLink>
            <NuxtLink to="/games">Games</NuxtLink>
            <a href="https://discord.gg/Eear4JW">Discord</a>
            <NuxtLink to="/support">Support Us</NuxtLink>
            <el-dropdown trigger="click">
                <a href="#">More</a>
                <el-dropdown-menu>
                    <el-dropdown-item>Rules</el-dropdown-item>
                    <el-dropdown-item>About Us</el-dropdown-item>
                    <el-dropdown-item>Steam Group</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
        <div class="user-items">
            <div v-if="user">
                <el-dropdown trigger="click">
                    <a class="user cursor-pointer">
                        <a-avatar :src="user.avatar"/>
                        <span>{{user.name}}</span>
                    </a>
                    <el-dropdown-menu>
                        <dropdown-item :to="`/user/${user.id}`">Profile</dropdown-item>
                        <dropdown-item>Liked Mods</dropdown-item>
                        <dropdown-item>Followed Mods</dropdown-item>
                        <div class="dropdown-splitter"/>
                        <dropdown-item to="/user-settings">User Settings</dropdown-item>
                        <dropdown-item>ModCP</dropdown-item>
                        <dropdown-item @click="logout">Log Out</dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div v-else>
                <NuxtLink to="/login">Login</NuxtLink>
                <NuxtLink to="/register">Register</NuxtLink>
            </div>
        </div>
    </header>
</template>
<script setup>
import { useStore } from '../../store';
import { computed } from '@nuxtjs/composition-api';

const logo = computed(() => '/mws_logo_white.svg'); //TODO: redo color mode

const store = useStore();
const user = computed(() => store.user);

async function logout() {
    await this.$axios.post('/logout');
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