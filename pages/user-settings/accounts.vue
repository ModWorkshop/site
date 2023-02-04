<template>
    <flex column gap="2">
        <a-alert :desc="$t('accounts_desc')" color="info"/>
        <flex column>
            <flex v-for="[name, provider] of Object.entries(providers)" :key="name" class="list-button items-center">
                <flex column>
                    <flex class="items-center">
                        <a-icon :icon="`mdi:${name}`" class="text-3xl"/>
                        {{provider.name}}
                    </flex>
                    <span v-if="provider.account">
                        <i18n-t v-if="provider.account" keypath="linked_time_ago" tag="span">
                            <template #time_ago>
                                <time-ago :time="provider.account.created_at"/>
                            </template>
                        </i18n-t>
                    </span>
                </flex>
                
                <VTooltip v-if="provider.account" :disabled="canUnlink" content="" class="my-auto ml-auto">
                    <div>
                        <a-button :disabled="!canUnlink" @click="unlink(name)">{{$t('unlink')}}</a-button>
                    </div>
                    <template #popper>{{$t('cannot_unlink_reason')}}</template>
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
import { useI18n } from 'vue-i18n';
import { SocialLogin, UserForm } from '~~/types/models';

const props = defineProps<{
    user: UserForm
}>();

const { public: config } = useRuntimeConfig();
const yesNoModal = useYesNoModal();
const listenToTabs = ref(false);
const { t } = useI18n();

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
        title: t('are_you_sure'),
        desc: t('unlink_warn'),
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