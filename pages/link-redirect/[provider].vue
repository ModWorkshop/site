<template>
    <page-block :column="false">
        <flex column class="mx-auto">
            <template v-if="!done">
                <h2 class="mx-auto">{{$t('linking_account')}}</h2>
                <a-loading/>
            </template>
            <span v-else>{{$t('done_linking_account')}}</span>
        </flex>
    </page-block>
</template>
<script setup lang="ts">
import { useI18n } from 'vue-i18n';

definePageMeta({
    middleware: 'users-only'
});

const route = useRoute();
const done = ref(false);
const { t } = useI18n();

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
            409: t('account_already_linked'),
        });
    }
}
</script>