<template>
	<m-form-modal
		v-model="vm"
		:title="$t('confirm_password')"
		:desc="$t('confirm_password_desc')"
		size="sm"
		:can-submit="pass.length > 0"
		:close-on-click-outside="false"
		@submit="submit"
	>
		<m-input v-model="pass" :label="$t('password')" type="password"/>
	</m-form-modal>
</template>

<script setup lang="ts">
import { AxiosError } from 'axios';

const vm = defineModel<boolean>({ required: true });
const emit = defineEmits<{
	(e: 'success'): void;
}>();
const pass = ref('');
const showModal = useQuickErrorToast();

async function submit() {
	try {
		await postRequest('user/confirm-password', { password: pass.value });
		emit('success');
	} catch (e) {
		if (e instanceof AxiosError) {
			if (e.status === 422) { // Password is wrong 100%
				showModal('password_error_wrong');
				return;
			}
		}
		showModal(e); // Fuck knows
	}
}
</script>
