<template>
    <page-block>
        <Title>{{$t('support_us')}}</Title>
        <flex gap="3" class="items-center" column>
            <a-img alt="logo" :src="logo" width="128" height="128"/>
            <h1 class="text-primary m-auto">{{$t('support_mws')}}!</h1>
            <h2>{{$t('supporter_desc')}}</h2>
            <a-alert v-if="user.supporter" color="success" :icon="false">
                <i18n-t keypath="already_support" tag="div" class="whitespace-pre text-center">
                    <time-ago null-is-never :time="user.supporter.expire_date"/>
                </i18n-t>
            </a-alert>
            <h2>{{$t('supporter_plans')}}</h2>
            {{$t('supporter_plans_desc')}}
            <flex gap="5">
                <flex column class="items-center">
                    <h3>{{$t('supporter_plan_1')}}</h3>
                    <span>$3 USD</span>
                </flex>
                <flex column class="items-center">
                    <h3>{{$t('supporter_plan_2')}}</h3>
                    <span>$8 USD</span>
                </flex>
                <flex column class="items-center">
                    <h3>{{$t('supporter_plan_3')}}</h3>
                    <span>$15 USD</span>
                </flex>
            </flex>
            <h2>{{$t('supporter_perks')}}</h2>
            <ul class="no-bullets">
                <li>{{$t('supporter_perk_1')}}</li>
                <li>{{$t('supporter_perk_2')}}</li>
                <li>{{$t('supporter_perk_3')}}</li>
                <li>{{$t('supporter_perk_4')}}</li>
            </ul>
            <span>{{$t('supporter_make_sure')}}</span>
            <donation-button link="https://ko-fi.com/luffydafloffi"/>
            <strong>{{$t('supporter_make_sure')}}</strong>
            <h2>{{$t('supporter_important')}}</h2>
            <ul class="no-bullets">
                <li>{{$t('supporter_important_1')}}</li>
                <li>{{$t('supporter_important_2')}}</li>
                <li>{{$t('supporter_important_3')}}</li>
            </ul>
            <h2>{{$t('supporter_currently')}}</h2>
            <flex wrap class="mb-3" style="max-width: 500px;">
                <NuxtLink v-for="supporter of supporters.data" :key="supporter.id" :to="`user/${supporter.user.unique_name}`">
                    <a-avatar :src="supporter.user.avatar"/>
                </NuxtLink>
            </flex>
        </flex>
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Supporter } from '~~/types/models';

const { user } = useStore();
const store = useStore();
const logo = computed(() => store.theme === 'light' ? 'assets/mws_logo_black.svg' : 'assets/mws_logo_white.svg');

const { data: supporters } = await useFetchMany<Supporter>('supporters');
</script>

<style scoped>
.no-bullets {
    text-align: center;
    list-style-type: none;
    padding: 0;
    margin: 0;
}
</style>