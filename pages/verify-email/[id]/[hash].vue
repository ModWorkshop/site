<template>
    <page-block>
        <m-flex column class="mx-auto">
            <h2 class="mx-auto">{{$t(verifying ? 'verifying_email' : 'verified_email')}}</h2>
            <m-button :loading="verifying" class="mx-auto" to="/">{{$t('back_to_home')}}</m-button>
        </m-flex>
    </page-block>
</template>
<script setup lang="ts">
import { FetchError } from 'ofetch';
import { useStore } from '~~/store';

definePageMeta({
    middleware: 'users-only'
});

const route = useRoute();
const { user, reloadUser } = useStore();

const verifying = ref(true);

if (route.query.error) {
    showError({ statusCode: 500, statusMessage: route.query.error_description as string });
}

if (import.meta.client && user) {
    try {
        await useGet(`/email/verify/${route.params.id}/${route.params.hash}`, { params: route.query});
        verifying.value = false;
        reloadUser();
    } catch (e) {
        if (e instanceof FetchError && e.response) {
            showError({ statusCode: e.response.status, statusMessage: e.response.statusText });
        }
    }
}
</script>