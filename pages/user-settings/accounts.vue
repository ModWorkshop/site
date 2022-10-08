<template>
    <flex column gap="2">
        <a-alert desc="Connect accounts from supported services in order to sign-in using them." color="info"/>
        <flex column>
            <flex v-for="[name, provider] of Object.entries(providers)" :key="name" class="list-button items-center">
                <flex column>
                    <flex class="items-center">
                        <font-awesome-icon :icon="['fab', name]" class="text-3xl"/>
                        {{provider.name}}
                    </flex>
                    <span v-if="provider.account">
                        Linked <time-ago :time="provider.account.created_at"/>
                    </span>
                </flex>
                
                <VTooltip v-if="provider.account" :disabled="canUnlink" content="" class="my-auto ml-auto">
                    <div>
                        <a-button :disabled="!canUnlink" @click="unlink(name)">{{$t('unlink')}}</a-button>
                    </div>
                    <template #popper>To unlink this account you must setup email and password or link a different service.</template>
                </VTooltip>
                <a-button 
                    v-else
                    :to="`${config.apiUrl}/social-logins/${name}/link-redirect`"
                    target="_blank"
                    class="ml-auto" 
                    @click="onLinkOpen"
                >
                    {{$t('link')}}
                </a-button>
            </flex>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { SocialLogin, UserForm } from '~~/types/models';

const props = defineProps<{
    user: UserForm
}>();

const { public: config } = useRuntimeConfig();
const yesNoModal = useYesNoModal();
const listenToTabs = ref(false);

const { data: accounts, refresh } = await useFetchData<SocialLogin[]>('social-logins');
const canUnlink = computed(() =>  accounts.value.length > 1 || props.user.signable);

const providers = computed(() => {
    const providers: Record<string, { name: string, account?: SocialLogin }> = {
        github: { name: 'GitHub' },
        gitlab: { name: 'GitLab' },
        steam: {  name: 'Steam' },
        discord: {  name: 'Discord' }
    };

    for (const account of accounts.value) {
        providers[account.social_id].account = account;
    }
    
    return providers;
});

function unlink(provider: string) {
    yesNoModal({
        desc: 'This will unlink the account from your ModWorkshop account and you will not be able to use it to login to this account anymore!',
        async yes() {
            await useDelete(`social-logins/${provider}`);
            accounts.value = accounts.value.filter(account => account.social_id !== provider);
        }
    });
}

function onLinkOpen() {
    listenToTabs.value = true;
}

onMounted(() => {
    if (process.client) {
        document.addEventListener("visibilitychange", function() {
            if (!document.hidden && listenToTabs.value) {
                refresh();
                listenToTabs.value = false;
            }
        });
    }
});

</script>