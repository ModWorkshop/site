<template>
    <page-block size="sm">
        <Title>{{$t('user_settings')}}</Title>
        <content-block class="p-8">
            <a-form :model="user" float-save-gui autocomplete="off" @submit="save">
                <a-nav side :root="isMe ? `/user-settings` : `/user/${user.id}/edit`">
                    <a-nav-link to="" :title="$t('account_tab')"/>
                    <a-nav-link to="profile" :title="$t('profile')"/>
                    <a-nav-link v-if="isMe" to="content" :title="$t('content_tab')"/>
                    <a-nav-link v-if="isMe" to="accounts" :title="$t('connected_accounts_tab')"/>
                    <!-- <a-nav-link to="api" :title="$t('api_access_tab')"/> -->
                    <template #content>
                        <NuxtPage :user="user"/>
                    </template>
                </a-nav>
            </a-form>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import clone from 'rfdc/default';
import { serializeObject } from '~~/utils/helpers';
import { useStore } from '../store';
import { User, UserForm } from '../types/models';

const showToast = useQuickErrorToast();

definePageMeta({
    middleware: 'users-only'
});

const store = useStore();

const route = useRoute();
const isMe = !route.params.user;

provide('isMe', isMe);

const { data } = await useResource<User>('user', 'users', undefined, undefined, clone(store.user));
const user = ref<UserForm>({
    ...data.value,
    password: '',
    current_password: '',
    confirm_password: '',
    avatar_file: null,
    banner_file: null,
});

async function save() {
    try {
        const nextUser = await patchRequest<User>(`users/${user.value.id}`, serializeObject(user.value));

        if (isMe) {
            store.user = clone(nextUser);
        }

        user.value = {
            ...nextUser,
            password: '',
            current_password: '',
            confirm_password: ''
        };
    } catch (error) {
        showToast(error);
    }
}
</script>