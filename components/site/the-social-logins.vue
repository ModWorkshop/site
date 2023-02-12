<template>
    <flex class="text-xl">
        <a-button :to="`${config.apiUrl}/social-logins/steam/login-redirect`" icon="cib:steam"/>
        <a-button :to="`${config.apiUrl}/social-logins/github/login-redirect`" icon="cib:github"/>
        <a-button :to="`${config.apiUrl}/social-logins/gitlab/login-redirect`" icon="cib:gitlab"/>
        <a-button :to="`${config.apiUrl}/social-logins/discord/login-redirect`" icon="cib:discord"/>
    </flex>
</template>

<script setup lang="ts">
const { public: config } = useRuntimeConfig();
</script>