<template>
    <page-block class="!w-1/3">
        <Title>{{$t('login')}}</Title>
        <a-form @submit="login">
            <h1>{{$t('login')}}</h1>
            <content-block column gap="3">
                <a-input v-model="user.email" :label="$t('email')" type="email"/>
                <a-input v-model="user.password" :label="$t('password')" type="password"/>
                <flex column gap="2">
                    {{$t('login_using_services')}}
                    <the-social-logins/>
                </flex>
                <a-input v-model="user.remember" :label="$t('remember_me')" type="checkbox"/>
                <div>
                    <a-button type="submit" :disabled="!canLogin">{{$t('login')}}</a-button>
                </div>
            </content-block>
        </a-form>    
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';

definePageMeta({
    middleware: 'guests-only'
});

const showErrorToast = useQuickErrorToast();

const { t } = useI18n();

const user = reactive({
    email: '',
    password: '',
    remember: true,
});

const canLogin = computed(() => user.email && user.password);
const store = useStore();

async function login() {
    try {
        await usePost('/login', user);
        store.attemptLoginUser();
    } catch (e) {
        showErrorToast(e, {
            401: t('login_error_401'),
            422: t('login_error_422')
        });
    }
}
</script>