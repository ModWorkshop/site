<template>
    <page-block :column="false" class="items-center">
		<h2 class="mx-auto">Login Succesful. Please wait a moment...</h2>
    </page-block>
</template>
<script setup lang="ts">
//TEMPORARY SOLUTION!
import { useStore } from '../store';

definePageMeta({
    middleware: 'guests-only'
});

const store = useStore();
const route = useRoute();

if (route.query.error) {
    showError({ statusCode: 500, statusMessage: route.query.error_description as string });
}

if (process.client) {
    const newQuery = {};

    //For some reason we receive it as openid. but Laravel socialite expects it as openid_
    for (const [key, value] of Object.entries(route.query)) {
        newQuery[key.replace('openid.', 'openid_')] = value;
    }
    
    await usePost(`/auth/${route.params.provider}/callback`, newQuery).catch(err => console.log(err));
    store.attemptLoginUser('/');
}
</script>