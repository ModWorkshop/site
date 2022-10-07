<template>
    <page-block class="!w-1/3">
        <a-form @submit="login">
            <h1>{{$t('login')}}</h1>
            <content-block column gap="3">
                <a-input v-model="user.email" label="Email"/>
                <a-input v-model="user.password" label="Password" type="password"/>
                <flex column gap="2">
                    Or register using one the following
                    <the-social-logins/>
                </flex>
                <a-input v-model="user.remember" label="Remember Me" type="checkbox"/>
                <a-alert v-if="error" color="danger" class="w-full">{{error}}</a-alert>
                <div>
                    <a-button type="submit" :disabled="!canLogin">{{$t('login')}}</a-button>
                </div>
            </content-block>
        </a-form>    
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';

definePageMeta({
    middleware: 'guests-only'
});

const user = reactive({
    email: '',
    password: '',
    remember: true,
});

const error = ref('');
const canLogin = computed(() => user.email && user.password);
const store = useStore();

async function login() {
    error.value = '';
    try {
        await usePost('/login', user);
        store.attemptLoginUser();
    } catch (e) {
        const codes = {
            401: 'Incorrect email or password',
            422: 'Given email or password are invalid'
        };
        console.log(e.response);
        console.log(codes[e.response.status] || 'Something went wrong');
        
        error.value = codes[e.response.status] || 'Something went wrong';
        return;
    }
}
</script>