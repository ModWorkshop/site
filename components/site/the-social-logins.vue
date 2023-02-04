<template>
    <flex class="text-xl">
        <a-button :to="`${config.apiUrl}/social-logins/steam/login-redirect`" icon="mdi:steam"/>
        <a-button :to="`${config.apiUrl}/social-logins/github/login-redirect`" icon="mdi:github"/>
        <a-button :to="`${config.apiUrl}/social-logins/gitlab/login-redirect`" icon="mdi:gitlab"/>
        <a-button :to="`${config.apiUrl}/social-logins/discord/login-redirect`" icon="mdi:discors"/>
    </flex>
</template>

<script setup lang="ts">
const { public: config } = useRuntimeConfig();
</script>