<template>
    <m-flex class="text-2xl">
        <m-button :to="`${url}/social-logins/steam/login-redirect`"><i-ri-steam-fill/></m-button>
        <m-button :to="`${url}/social-logins/github/login-redirect`"><i-ri-github-fill/></m-button>
        <m-button :to="`${url}/social-logins/gitlab/login-redirect`"><i-ri-gitlab-fill/></m-button>
        <m-button :to="`${url}/social-logins/discord/login-redirect`"><i-ri-discord-fill/></m-button>
    </m-flex>
</template>

<script setup lang="ts">
const { public: config } = useRuntimeConfig();

const url = computed(() => config.apiUrl);
</script>