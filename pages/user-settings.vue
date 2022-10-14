<template>
    <page-block size="sm">
        <Title>{{$t('user_settings')}}</Title>
        <content-block class="p-8">
            <a-form :model="user" float-save-gui @submit="save">
                <a-nav side :root="isMe ? `/user-settings` : `/user/${user.id}/edit`">
                    <a-nav-link to="" :title="$t('account_tab')"/>
                    <a-nav-link to="profile" :title="$t('profile')"/>
                    <a-nav-link to="content" :title="$t('content_tab')"/>
                    <a-nav-link to="accounts" :title="$t('connected_accounts_tab')"/>
                    <template #content>
                        <NuxtPage keepalive :user="user"/>
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

const { showToast } = useToaster();

definePageMeta({
    middleware: 'users-only'
});

const store = useStore();

const isMe = ref(false);
const route = useRoute();

provide('isMe', isMe);

const { data } = await useResource<User>('user', 'users', null, null, clone(store.user));
const user = ref<UserForm>({
    ...data.value,
    password: '',
    current_password: '',
    confirm_password: '',
    avatar_file: null,
    banner_file: null,
});

isMe.value = !route.params.userId;

async function save() {
    try {
        const nextUser = await usePatch<User>(`users/${user.value.id}`, serializeObject(user.value));

        if (isMe.value) {
            store.user = clone(nextUser);
        }

        user.value = {
            ...nextUser,
            password: '',
            current_password: '',
            confirm_password: ''
        };
    } catch (error) {
        showToast({ desc: error.message, color: 'danger' });
    }
}
</script>