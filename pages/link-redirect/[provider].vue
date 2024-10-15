<template>
    <page-block>
        <m-flex column class="mx-auto">
            <template v-if="!done">
                <h2 class="mx-auto">{{$t('linking_account')}}</h2>
                <m-loading/>
            </template>
            <span v-else>{{$t('done_linking_account')}}</span>
        </m-flex>
    </page-block>
</template>
<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store/index';

definePageMeta({
    middleware: 'users-only'
});

const route = useRoute();
const done = ref(false);
const { t } = useI18n();
const { user } = useStore();

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
        await postRequest(`/social-logins/${route.params.provider}/link-callback`, serializeObject(newQuery));
        done.value = true;
        user!.activated = true;
    } catch (e) {
        useHandleError(e, {
            409: t('account_already_linked'),
        });
    }
}
</script>