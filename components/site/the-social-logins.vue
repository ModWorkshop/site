<template>
    <flex>
        <a-button :to="`${config.apiUrl}/social-logins/steam/login-redirect`" :icon="['fab', 'steam']" icon-size="lg"/>
        <a-button :to="`${config.apiUrl}/social-logins/github/login-redirect`" :icon="['fab', 'github']" icon-size="lg"/>
        <a-button :to="`${config.apiUrl}/social-logins/gitlab/login-redirect`" :icon="['fab', 'gitlab']" icon-size="lg"/>
        <a-button :to="`${config.apiUrl}/social-logins/discord/login-redirect`" :icon="['fab', 'discord']" icon-size="lg"/>
    </flex>
</template>

<script setup lang="ts">
const { public: config } = useRuntimeConfig();
</script>