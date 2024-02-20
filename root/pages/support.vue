<template>
    <page-block size="2xs">
        <Title>{{$t('support_us')}}</Title>
        <m-flex gap="3" class="items-center" column>
            <m-img alt="logo" :src="logo" width="128" height="128" is-asset/>
            <h1 class="text-primary m-auto">{{ $t('support_mws') }}</h1>
            <h2>{{$t('supporter_desc')}}</h2>
            <h3>{{$t('supporter_you_get')}}</h3>
            <ul>
                <li><b>{{$t('supporter_no_ads')}}</b></li>
                <li><b>{{$t('supporter_custom_name_color')}}</b></li>
                <li><b>{{$t('supporter_supporter_tag')}}</b></li>
            </ul>
            <m-dropdown type="tooltip" dropdown-class="p-2" :tool-tip-delay="0.1" :disabled="!!user">
                <m-button 
                    size="lg"
                    :to="user ? `https://sponsor.nitrocnct.com/subscribe?token=${user.nitro_token}&cancelUrl=${supportUrl}&successUrl=${supportUrl}` : undefined"
                    :disabled="!user"
                >
                    <i-mdi-heart-multiple/> {{ $t('supporter_via_nitro') }}
                </m-button>

                <template #content>{{$t('login_required')}}</template>
            </m-dropdown>

            <m-button v-if="!user" to="/login">{{$t('login')}}</m-button>

            <m-alert v-if="user?.active_supporter" color="success" :icon="false">
                <i18n-t keypath="supporter_already" tag="div" class="whitespace-pre text-center" scope="global">
                    <template #time>
                        <m-time-ago null-is-never :time="user.active_supporter.expire_date"/>
                    </template>
                </i18n-t>
            </m-alert>
            <template v-if="supporters?.data.length">
                <h2>{{$t('currently_supported')}}</h2>
                <m-flex wrap class="mb-3" style="max-width: 500px;">
                    <NuxtLink v-for="supporter of supporters.data" :key="supporter.id" :to="`user/${supporter.user.unique_name ?? supporter.user.id}`">
                        <m-avatar :src="supporter.user.avatar"/>
                    </NuxtLink>
                </m-flex>
            </template>

            <b>{{$t('supporter_just_support')}}</b>
            <donation-button link="paypal.me/tsunavr"/>
            <!-- <donation-button link="https://ko-fi.com/luffydafloffi"/> -->

            <m-alert>
                <b>{{$t('supporter_faq_q_1')}}</b>
                <i>{{$t('supporter_faq_a_1')}}</i>
                <b>{{$t('supporter_faq_q_2')}}</b>
                <i>{{$t('supporter_faq_a_2')}}</i>
                <b>{{$t('supporter_faq_q_3')}}</b>
                <i>{{$t('supporter_faq_a_3')}}</i>
            </m-alert>
        </m-flex>
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import type { Supporter } from '~~/types/models';

const { public: config } = useRuntimeConfig();
const { user } = useStore();
const store = useStore();
const logo = computed(() => store.theme === 'light' ? 'mws_logo_black.svg' : 'mws_logo_white.svg');
const supportUrl = `${config.siteUrl}/support`;

const { data: supporters } = await useFetchMany<Supporter>('supporters?active_only=1&sort_by_id=1');
if (user) {
    const { data: subData } = await useFetchData<Supporter>('supporters/nitro-check');
    user.active_supporter = subData.value ?? undefined;
}
</script>

<style scoped>
.no-bullets {
    text-align: center;
    list-style-type: none;
    padding: 0;
    margin: 0;
}
</style>