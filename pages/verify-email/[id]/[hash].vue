<template>
    <page-block :column="false">
        <flex column class="mx-auto">
            <h2 class="mx-auto">{{$t(verifying ? 'verifying_email' : 'verified_email')}}</h2>
            <a-loading v-if="verifying"/>
            <a-button v-else class="mx-auto" to="/">{{$t('back_to_home')}}</a-button>
        </flex>
    </page-block>
</template>
<script setup lang="ts">
import { DateTime } from 'luxon';
import { FetchError } from 'ohmyfetch';
import { useStore } from '~~/store';

definePageMeta({
    middleware: 'users-only'
});

const route = useRoute();
const { user } = useStore();

const verifying = ref(true);

if (route.query.error) {
    showError({ statusCode: 500, statusMessage: route.query.error_description as string });
}

if (process.client && user) {
    try {
        await useGet(`/email/verify/${route.params.id}/${route.params.hash}`, { params: route.query});
        verifying.value = false;
        user.email_verified_at = DateTime.now().toISODate();
    } catch (e) {
        if (e instanceof FetchError && e.response) {
            showError({ statusCode: e.response.status, statusMessage: e.response.statusText });
        }
    }
}
</script>