<template>
    <page-block>
        <Title>{{$t('support_us')}}</Title>
        <flex gap="3" class="items-center" column>
            <a-img alt="logo" :src="logo" width="128" height="128" is-asset/>
            <h1 class="text-primary m-auto">{{ $t('support_mws') }}</h1>
            <h2>{{$t('supporter_desc')}}</h2>
            <a-alert v-if="user?.active_supporter" color="success" :icon="false">
                <i18n-t keypath="supporter_already" tag="div" class="whitespace-pre text-center" scope="global">
                    <template #time>
                        <time-ago null-is-never :time="user.active_supporter.expire_date"/>
                    </template>
                </i18n-t>
            </a-alert>
            <span>{{$t('supporter_after_donating')}}</span>
            <!-- <donation-button link="https://ko-fi.com/luffydafloffi"/> -->
            <donation-button link="https://www.paypal.com/donate/?hosted_button_id=EU55X9RVECM4C"/>
            <template v-if="supporters?.data.length">
                <h2>{{$t('currently_supported')}}</h2>
                <flex wrap class="mb-3" style="max-width: 500px;">
                    <NuxtLink v-for="supporter of supporters.data" :key="supporter.id" :to="`user/${supporter.user.unique_name}`">
                        <a-avatar :src="supporter.user.avatar"/>
                    </NuxtLink>
                </flex>
            </template>
        </flex>
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Supporter } from '~~/types/models';

const { user } = useStore();
const store = useStore();
const logo = computed(() => store.theme === 'light' ? 'mws_logo_black.svg' : 'mws_logo_white.svg');

const { data: supporters } = await useFetchMany<Supporter>('supporters?active_only=1&sort_by_id=1');
</script>

<style scoped>
.no-bullets {
    text-align: center;
    list-style-type: none;
    padding: 0;
    margin: 0;
}
</style>