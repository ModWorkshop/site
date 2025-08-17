<template>
    <page-block>
        <m-flex v-if="!error" column class="mx-auto">
            <h2 class="mx-auto">{{$t('logging_you_in')}}</h2>
            <m-loading/>
        </m-flex>
    </page-block>
</template>
<script setup lang="ts">
import { useStore } from '~/store';
import { AxiosError } from 'axios';

definePageMeta({
    middleware: 'guests-only'
});

const store = useStore();
const route = useRoute();
const error = ref();

if (route.query.error) {
    showError({ statusCode: 500, statusMessage: route.query.error_description as string });
}

if (import.meta.client) {
    const newQuery = {};

    //For some reason we receive it as openid. but Laravel socialite expects it as openid_
    for (const [key, value] of Object.entries(route.query)) {
        newQuery[key.replace('openid.', 'openid_')] = value;
    }
    
    try {
        await postRequest(`/social-logins/${route.params.provider}/login-callback`, serializeObject(newQuery));
        await reloadToken();
        store.attemptLoginUser('/');
    } catch (e) {
        if (e instanceof AxiosError && e.response) {
            showError({ statusCode: e.response.status, statusMessage: e.response.statusText });
        }
    }
}
</script>