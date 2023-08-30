<template>
    <flex class="text-xl">
        <a-button :to="`${url}/social-logins/steam/login-redirect`"><i-cib-steam/></a-button>
        <a-button :to="`${url}/social-logins/github/login-redirect`"><i-cib-github/></a-button>
        <a-button :to="`${url}/social-logins/gitlab/login-redirect`"><i-cib-gitlab/></a-button>
        <a-button :to="`${url}/social-logins/discord/login-redirect`"><i-cib-discord/></a-button>
    </flex>
</template>

<script setup lang="ts">
const { public: config } = useRuntimeConfig();

const url = computed(() => config.apiUrl);
</script>