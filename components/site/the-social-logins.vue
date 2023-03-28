<template>
    <flex class="text-xl">
        <a-button :to="`${url}/social-logins/steam/login-redirect`" icon="cib:steam"/>
        <a-button :to="`${url}/social-logins/github/login-redirect`" icon="cib:github"/>
        <a-button :to="`${url}/social-logins/gitlab/login-redirect`" icon="cib:gitlab"/>
        <a-button :to="`${url}/social-logins/discord/login-redirect`" icon="cib:discord"/>
    </flex>
</template>

<script setup lang="ts">
const { public: config } = useRuntimeConfig();

const url = computed(() => config.apiUrl);
</script>