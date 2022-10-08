<template>
    <page-block :column="false">
        <flex column class="mx-auto">
            <template v-if="!done">
                <h2 class="mx-auto">Linking account. Please wait a moment...</h2>
                <a-loading/>
            </template>
            <span v-else>Done! Please close the tab and return to the previous tab.</span>
        </flex>
    </page-block>
</template>
<script setup lang="ts">
definePageMeta({
    middleware: 'users-only'
});

const route = useRoute();
const done = ref(false);

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
        await usePost(`/social-logins/${route.params.provider}/link-callback`, newQuery);
        done.value = true;
    } catch (e) {
        useHandleError(e, {
            409: 'Account was already linked or the provider was already linked.',
            400: 'Something went wrong.'
        });
    }
}
</script>