<template>
	<password-confirm-modal v-model="showPassConfirm" @success="passConfirmed"/>
	<m-form-modal
		v-model="showEnableTwoFactor"
		:title="$t('enable_2fa')"
		:can-submit="twoFactorConfirmCode.toString().length > 0"
		size="sm"
		:close-on-click-outside="false"
		@submit="submitConfirm"
		@closed="closeModal('enable')"
	>
		<template #desc>
			<i18n-t keypath="enable_2fa_modal_desc" tag="span" class="whitespace-pre-line">
				<template #authy>
					<NuxtLink href="https://www.authy.com/" target="_blank">Authy</NuxtLink>
				</template>
				<template #microsoft>
					<NuxtLink href="https://www.microsoft.com/en-us/security/mobile-authenticator-app" target="_blank">Microsoft Authenticator</NuxtLink>
				</template>
				<template #google>
					<NuxtLink href="https://support.google.com/accounts/answer/1066447?hl=en" target="_blank">Google Authenticator</NuxtLink>
				</template>
			</i18n-t>
		</template>
		<m-flex class="items-center self-center p-4 rounded" style="background-color: #fff;">
			<img v-if="svgData" :src="svgData.svg" width="256" class="self-center">
		</m-flex>
		{{ $t('enable_2fa_manual') }}
		<h3>
			{{ key }}
		</h3>
		<m-input v-model="twoFactorConfirmCode" :label="$t('2fa_confirm_code')"/>
	</m-form-modal>

	<m-modal
		v-model="isShowRecoveryModal"
		:title="$t('2fa_recovery_codes')"
		size="sm"
		@closed="closeModal('recovery-codes')"
	>
		{{ $t('2fa_recovery_codes_desc') }}
		<m-flex wrap class="justify-around">
			<h3 v-for="code of recoveryCodes" :key="code">
				{{ code }}
			</h3>
		</m-flex>

		<m-button @click="regenerateCodes">{{ $t('2fa_recovery_codes_regenerate') }}</m-button>
	</m-modal>
</template>

<script setup lang="ts">
import { AxiosError } from 'axios';
import { useStore } from '~/store';

const twoFactorConfirmCode = ref('');

const vm = defineModel<'enable' | 'disable' | 'recovery-codes' | 'hidden'>({ required: true, default: 'hidden' });

const { user } = defineProps<{
	user: User;
}>();

const { reloadUser } = useStore();

const showEnableTwoFactor = ref(false);
const showPassConfirm = ref(false);
const svgData = ref<TwoFactorResponse>();
const key = ref('');

const isShowRecoveryModal = ref(false);
const recoveryCodes = ref<string[]>();

const showError = useQuickErrorToast();
const { showToast } = useToaster();
const showModal = useYesNoModal();

type TwoFactorResponse = {
	svg: string;
	url: string;
};

watch(vm, () => {
	showPassConfirm.value = false;
	showEnableTwoFactor.value = false;
	isShowRecoveryModal.value = false;

	handleActions();
});

function handleActions() {
	const val = vm.value;

	if (val === 'enable') {
		showEnableTwoFactorAuthModal();
	} else if (val === 'disable') {
		showModal({
			title: $t('disable_2fa_modal'),
			desc: $t('irreversible_action'),
			yes: disableTwoFactorAuth,
			no: async () => closeModal('disable')
		});
	} else if (val === 'recovery-codes') {
		showRecoveryCodesModal();
	}
}

function passConfirmed() {
	showPassConfirm.value = false;
	handleActions();
}

function closeModal(prev: string) {
	if (prev === vm.value) {
		vm.value = 'hidden';
	}
}

async function disableTwoFactorAuth() {
	try {
		await deleteRequest('/user/two-factor-authentication');
		user.two_factor_confirmed_at = undefined;
		vm.value = 'hidden';
	} catch (e) {
		if (!checkNeedPasswordConfirmation(e)) {
			showError(e);
		}
		return;
	}
}

async function showEnableTwoFactorAuthModal() {
	try {
		await postRequest('/user/two-factor-authentication');
	} catch (e) {
		if (!checkNeedPasswordConfirmation(e)) {
			showError(e);
		}
		return;
	}

	const data = await getRequest<TwoFactorResponse>('/user/two-factor-qr-code');
	const blob = new Blob([data.svg], { type: 'image/svg+xml' });
	svgData.value = {
		svg: URL.createObjectURL(blob),
		url: data.url
	};

	const url = new URL(data.url);
	const secret = url.searchParams.get('secret')!;
	let k = '';
	for (let i = 0; i < secret.length / 4; i++) {
		k += (i === 0 ? '' : '\t') + secret.substring(i * 4, (i + 1) * 4);
	}
	key.value = k;

	showEnableTwoFactor.value = true;
}

function checkNeedPasswordConfirmation(e) {
	if (e instanceof AxiosError) {
		if (e.status === 423) { // Need password confirmation
			showPassConfirm.value = true;
			return true;
		}
	}
	return false;
}

async function showRecoveryCodesModal() {
	try {
		recoveryCodes.value = await getRequest<string[]>('/user/two-factor-recovery-codes');
	} catch (e) {
		if (!checkNeedPasswordConfirmation(e)) {
			showError(e);
		}
		return;
	}

	isShowRecoveryModal.value = true;
}

async function regenerateCodes() {
	try {
		recoveryCodes.value = await postRequest<string[]>('/user/two-factor-recovery-codes');
		showRecoveryCodesModal();
	} catch (e) {
		if (!checkNeedPasswordConfirmation(e)) {
			showError(e);
		}
	}
}

async function submitConfirm() {
	try {
		await postRequest('/user/confirmed-two-factor-authentication', {
			code: twoFactorConfirmCode.value
		});

		const storeUser = await reloadUser();
		user.two_factor_confirmed_at = storeUser?.two_factor_confirmed_at;

		twoFactorConfirmCode.value = '';
		vm.value = 'recovery-codes';
	} catch (e) {
		if (e instanceof AxiosError) {
			if (e.status === 422) { // Password is wrong 100%
				showToast({
					color: 'danger',
					desc: $t('2fa_invalid_code')
				});
				return;
			}
		}
		showError(e); // Fuck knows
	}
}
</script>
