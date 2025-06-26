<template>
    <m-flex column gap="2">
        <m-alert :desc="$t('accounts_desc')" color="info"/>
        <m-flex column>
            <m-flex v-for="[name, provider] of Object.entries(providers)" :key="name" class="list-button items-center">
                <m-flex column>
                    <m-flex class="items-center">
                        <m-icon :icon="provider.icon" class="text-3xl"/>
                        {{provider.name}}
                    </m-flex>
                    <span v-if="provider.account">
                        <i18n-t v-if="provider.account" keypath="linked_time_ago" tag="span" scope="global">
                            <template #time_ago>
                                <m-time :datetime="provider.account.created_at" relative/>
                            </template>
                        </i18n-t>
                    </span>
                </m-flex>
                
                <m-dropdown v-if="provider.account" type="tooltip" :disabled="canUnlink" class="my-auto ml-auto">
                    <div>
                        <m-button :disabled="!canUnlink" @click="unlink(name)">{{$t('unlink')}}</m-button>
                    </div>
                    <template #content>{{$t('cannot_unlink_reason')}}</template>
                </m-dropdown>
                <m-button 
                    v-else
                    :to="`${config.apiUrl}/social-logins/${name}/link-redirect`"
                    target="_blank"
                    class="ml-auto" 
                    @click="onLinkOpen"
                >
                    {{$t('link')}}
                </m-button>
            </m-flex>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { SocialLogin, UserForm } from '~/types/models';
import iconGitHub from '~icons/ri/github-fill';
import iconGitLab from '~icons/ri/gitlab-fill';
import iconDiscord from '~icons/ri/discord-fill';
import iconSteam from '~icons/ri/steam-fill';

const props = defineProps<{
    user: UserForm
}>();

const isMe = inject<boolean>('isMe');

if (!isMe) {
    useNoPermsPage();
}

const { public: config } = useRuntimeConfig();
const yesNoModal = useYesNoModal();
const listenToTabs = ref(false);
const { t } = useI18n();

const { data: accounts, refresh } = await useFetchData<SocialLogin[]>('social-logins');
const canUnlink = computed(() =>  accounts.value && accounts.value.length > 1 || props.user.signable);

const providers = computed(() => {
    const providers: Record<string, { name: string, account?: SocialLogin, icon: Component }> = {
        github: { name: 'GitHub', icon: iconGitHub },
        gitlab: { name: 'GitLab', icon: iconGitLab },
        steam: {  name: 'Steam', icon: iconSteam },
        discord: {  name: 'Discord', icon: iconDiscord }
    };

    if (accounts.value) {
        for (const account of accounts.value) {
            providers[account.social_id].account = account;
        }
    }
    
    return providers;
});

function unlink(provider: string) {
    yesNoModal({
        title: t('are_you_sure'),
        desc: t('unlink_warn'),
        async yes() {
            await deleteRequest(`social-logins/${provider}`);
            if (accounts.value) {
                accounts.value = accounts.value.filter(account => account.social_id !== provider);
            }
        }
    });
}

function onLinkOpen() {
    listenToTabs.value = true;
}

onMounted(() => {
    if (import.meta.client) {
        document.addEventListener("visibilitychange", function() {
            if (!document.hidden && listenToTabs.value) {
                refresh();
                listenToTabs.value = false;
            }
        });
    }
});

</script>