<template>
    <page-block size="md">
        <Title>{{$t('user_settings')}}</Title>
        <m-form v-model="user" v-model:flush-changes="flushChanges" float-save-gui autocomplete="off" @submit="save">
            <m-nav side :root="isMe ? `/user-settings` : `/user/${user.id}/edit`">
                <m-nav-link to="profile" alias="" :title="$t('profile')"/>
                <m-nav-link to="account" :title="$t('account_tab')"/>
                <m-nav-link v-if="isMe" to="settings" :title="$t('settings')"/>
                <m-nav-link v-if="isMe" to="content" :title="$t('content_tab')"/>
                <m-nav-link v-if="isMe" to="accounts" :title="$t('connected_accounts_tab')"/>
                <!-- <m-nav-link to="api" :title="$t('api_access_tab')"/> -->
                <template #content>
                    <NuxtPage :user="user"/>
                </template>
            </m-nav>
        </m-form>
    </page-block>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';
import { useStore } from '../store';
import type { User, UserForm } from '../types/models';

const showToast = useQuickErrorToast();

definePageMeta({
    middleware: 'users-only'
});

const store = useStore();
const route = useRoute();
const flushChanges = createEventHook();
const isMe = !route.params.user;

provide('isMe', isMe);

const { data } = await useResource<User>('user', 'users', undefined, undefined, clone(store.user));
const user = ref<UserForm>({
    ...data.value,
    password: '',
    current_password: '',
    confirm_password: '',
    avatar_file: undefined,
    banner_file: undefined,
    background_file: undefined,
});

async function save() {
    try {
        const nextUser = await patchRequest<User>(`users/${user.value.id}`, serializeObject(user.value));

        if (isMe) {
            store.user = clone(nextUser);
        }

        flushChanges.trigger({
            ...nextUser,
            password: '',
            current_password: '',
            confirm_password: '',
            avatar_file: undefined,
            banner_file: undefined,
            background_file: undefined,
        });
    } catch (error) {
        showToast(error);
    }
}
</script>