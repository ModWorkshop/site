<template>
    <VueHcaptcha ref="captchaRef" :sitekey="config.hcaptchaSiteKey" @verify="onVerifyCaptcha"/>
</template>

<script setup lang="ts">
import VueHcaptcha from '@hcaptcha/vue3-hcaptcha';

const { public: config } = useRuntimeConfig();
const captchaRef = ref<VueHcaptcha|null>(null);
const currentToken = defineModel<string>();

function onVerifyCaptcha(token) {
    currentToken.value = token;
}

watch(currentToken, val => {
    if (!val) {
        captchaRef.value?.reset();
    }
});
</script>