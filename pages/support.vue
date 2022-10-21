<template>
    <page-block>
        <Title>{{$t('support_us')}}</Title>
        <flex gap="3" class="items-center" column>
            <a-img alt="logo" :src="logo" width="128" height="128"/>
            <h1 class="text-primary m-auto">Support ModWorkshop!</h1>
            <h2>Wish to support us?</h2>
            <a-alert v-if="user?.supporter" color="success" :icon="false">
                <i18n-t keypath="supporter_already" tag="div" class="whitespace-pre text-center">
                    <template #time>
                        <time-ago null-is-never :time="user.supporter.expire_date"/>
                    </template>
                </i18n-t>
            </a-alert>
            <span>After donating, contact one of the moderators for a thank you.</span>
            <donation-button link="https://ko-fi.com/luffydafloffi"/>
            <h2>Cool people that currently support us</h2>
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