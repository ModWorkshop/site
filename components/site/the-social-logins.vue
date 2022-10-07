<template>
    <flex>
        <a-button :to="`${config.apiUrl}/auth/steam/redirect`" :icon="['fab', 'steam']" icon-size="lg"/>
        <a-button :to="`${config.apiUrl}/auth/github/redirect`" :icon="['fab', 'github']" icon-size="lg"/>
        <a-button :to="`${config.apiUrl}/auth/gitlab/redirect`" :icon="['fab', 'gitlab']" icon-size="lg"/>
        <a-button :to="`${config.apiUrl}/auth/discord/redirect`" :icon="['fab', 'discord']" icon-size="lg"/>
    </flex>
</template>

<script setup lang="ts">
const { public: config } = useRuntimeConfig();
</script>