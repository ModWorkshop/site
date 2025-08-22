<template>
	<page-block size="2xs">
		<Title>{{ $t('login') }}</Title>
		<m-form @submit="login">
			<h1>{{ $t('login') }}</h1>
			<m-content-block column gap="3">
				<m-input v-model="user.email" :label="$t('email')" type="email"/>
				<m-input v-model="user.password" :label="$t('password')" type="password"/>
				<m-flex>
					<m-input v-model="user.remember" :label="$t('remember_me')" type="checkbox"/>
					<NuxtLink to="forgot-password" class="ml-auto">{{ $t('forgot_password_button') }}</NuxtLink>
				</m-flex>
				<div>
					<m-button type="submit" :disabled="!canLogin">{{ $t('login') }}</m-button>
				</div>
				<m-flex column>
					<NuxtLink class="mt-2" to="register">{{ $t('dont_have_account') }}</NuxtLink>
					<m-flex column>
						{{ $t('login_using_services') }}
						<the-social-logins/>
					</m-flex>
				</m-flex>
				<a-captcha v-model="captchaToken"/>
			</m-content-block>
		</m-form>
	</page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~/store';

definePageMeta({
	middleware: 'guests-only'
});

const showErrorToast = useQuickErrorToast();
const allowCookies = useCookie('allow-cookies');

const { t } = useI18n();

const user = reactive({
	email: '',
	password: '',
	remember: Boolean(allowCookies.value) === true
});

const canLogin = computed(() => user.email && user.password);
const store = useStore();

const captchaToken = ref<string>('');

async function login() {
	try {
		await postRequest('/login', { ...user, 'h-captcha-response': captchaToken.value });
		store.attemptLoginUser();
		reloadToken();
	} catch (e) {
		showErrorToast(e, {
			401: t('login_error_401'),
			422: t('login_error_422')
		});
	}

	captchaToken.value = '';
}
</script>
