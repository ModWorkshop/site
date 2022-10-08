<template>
    <page-block :column="false">
        <flex v-if="!error" column class="mx-auto">
            <h2 class="mx-auto">Logging you in. Please wait a moment...</h2>
            <a-loading/>
        </flex>
    </page-block>
</template>
<script setup lang="ts">
import { useStore } from '../store';

definePageMeta({
    middleware: 'guests-only'
});

const store = useStore();
const route = useRoute();
const error = ref();

if (route.query.error) {
    showError({ statusCode: 500, statusMessage: route.query.error_description as string });
}

if (process.client) {
    const newQuery = {};

    //For some reason we receive it as openid. but Laravel socialite expects it as openid_
    for (const [key, value] of Object.entries(route.query)) {
        newQuery[key.replace('openid.', 'openid_')] = value;
    }
    
    try {
        await usePost(`/social-logins/${route.params.provider}/login-callback`, newQuery);
        store.attemptLoginUser('/');
    } catch (e) {
        console.log(e.response);
        
        showError({ statusCode: e.response.status, statusMessage: e.response.statusText });
    }
}
</script>