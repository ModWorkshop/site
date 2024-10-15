<template>
    <m-flex class="text-xl">
        <m-button :to="`${url}/social-logins/steam/login-redirect`"><i-cib-steam/></m-button>
        <m-button :to="`${url}/social-logins/github/login-redirect`"><i-cib-github/></m-button>
        <m-button :to="`${url}/social-logins/gitlab/login-redirect`"><i-cib-gitlab/></m-button>
        <m-button :to="`${url}/social-logins/discord/login-redirect`"><i-cib-discord/></m-button>
    </m-flex>
</template>

<script setup lang="ts">
const { public: config } = useRuntimeConfig();

const url = computed(() => config.apiUrl);
</script>