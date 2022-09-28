<template>
    <page-block size="sm">
        <content-block class="p-8">
            <a-form :model="user" float-save-gui @submit="save">
                <a-nav side root="/user-settings">
                    <a-nav-link to="" title="Account"/>
                    <a-nav-link to="profile" title="Profile"/>
                    <a-nav-link to="content" title="Content"/>
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
import { useStore } from '../store';
import { User, UserForm } from '../types/models';

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
            confirm_password: ''
        };
    } catch (error) {
        console.log(error);
    }
}
</script>