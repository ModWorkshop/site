<template>
	<m-form @submit="loginWithTwoFactor">
		<m-content-block column gap="3">
			<m-alert>{{ $t(useCode ? '2fa_info' : '2fa_recovery_info') }}</m-alert>
			<m-input v-if="useCode" v-model="twoFactorCode" :label="$t('2fa_code')"/>
			<m-input v-else v-model="twoFactorCode" :label="$t('2fa_recovery_code')"/>
			<m-button v-if="useCode" color="secondary" @click="useCode = !useCode">{{ $t('2fa_enter_recovery_code') }}</m-button>
			<m-button v-else color="secondary" @click="useCode = !useCode">{{ $t('2fa_enter_code') }}</m-button>
			<m-button type="submit" :disabled="!canLoginTwoFactor">{{ $t('login') }}</m-button>
		</m-content-block>
	</m-form>
</template>

<script setup lang="ts">
import { useStore } from '~/store';

const useCode = ref(true);
const twoFactorCode = ref('');
const canLoginTwoFactor = computed(() => useCode.value ? twoFactorCode.value.length >= 6 : twoFactorCode.value.length >= 12);
const showErrorToast = useQuickErrorToast();
const { attemptLoginUser } = useStore();

async function loginWithTwoFactor() {
	try {
		await postRequest('/two-factor-challenge', { [useCode.value ? 'code' : 'recovery_code']: twoFactorCode.value });

		await reloadToken();
		attemptLoginUser('/');
	} catch (e) {
		showErrorToast(e);
	}
}
</script>
