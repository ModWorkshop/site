<template>
    <page-block size="2xs">
        <Title>{{$t('login')}}</Title>
        <a-form @submit="login">
            <h1>{{$t('login')}}</h1>
            <content-block column gap="3">
                <a-input v-model="user.email" :label="$t('email')" type="email"/>
                <a-input v-model="user.password" :label="$t('password')" type="password"/>
                <flex>
                    <a-input v-model="user.remember" :label="$t('remember_me')" type="checkbox"/>
                    <NuxtLink to="forgot-password" class="ml-auto">{{$t('forgot_password_button')}}</NuxtLink>
                </flex>
                <div>
                    <a-button type="submit" :disabled="!canLogin">{{$t('login')}}</a-button>
                </div>
                <flex column>
                    <NuxtLink class="mt-2" to="register">{{$t('dont_have_account')}}</NuxtLink>
                    <flex column>
                        {{$t('login_using_services')}}
                        <the-social-logins/>
                    </flex>
                </flex>
                <NuxtTurnstile ref="turnstile" v-model="turnstileToken"/>
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
const allowCookies = useCookie('allow-cookies');

const { t } = useI18n();

const user = reactive({
    email: '',
    password: '',
    remember: Boolean(allowCookies.value) === true,
});

const canLogin = computed(() => user.email && user.password);
const store = useStore();

const turnstile = ref();
const turnstileToken = ref<string>();

async function login() {
    const token = turnstileToken.value;
    turnstileToken.value = '';

    try {
        await postRequest('/login', {...user, 'cf-turnstile-response': token});
        store.attemptLoginUser();
        reloadToken();
    } catch (e) {
        showErrorToast(e, {
            401: t('login_error_401'),
            422: t('login_error_422')
        });

        turnstile.value?.reset();
    }
}
</script>