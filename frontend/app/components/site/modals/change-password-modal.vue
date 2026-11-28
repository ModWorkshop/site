<template>
	<m-form-modal
		v-model="showModal"
		:title="$t('change_password')"
		:can-submit="canSubmit"
		size="sm"
		:close-on-click-outside="false"
		@submit="submitConfirm"
	>
		<m-flex column gap="3">
			<m-input
				v-if="needsCurrent"
				v-model="currentPassword"
				autocomplete="off"
				:label="$t('current_password')"
				type="password"
				maxlength="128"
			/>
			<m-input
				v-model="password"
				:validity="passValidity"
				autocomplete="off"
				:label="$t('new_password')"
				type="password"
				minlength="12"
				maxlength="128"
			/>
			<m-input
				v-model="confirmPassword"
				:validity="confirmPassValidity"
				:label="$t('confirm_password')"
				type="password"
				minlength="12"
				maxlength="128"
			/>

			<small>{{ $t('password_guide') }}</small>
		</m-flex>
	</m-form-modal>
</template>

<script setup lang="ts">
import { useStore } from '~/store';

const { user } = defineProps<{
	user: User;
}>();
const showModal = defineModel<boolean>({ required: true });

const { t } = useI18n();
const { user: me, setUser } = useStore();
const showError = useQuickErrorToast();

const isMe = computed(() => me!.id === user.id);
const needsCurrent = computed(() => isMe && user.has_password);
const currentPassword = ref('');
const password = ref('');
const confirmPassword = ref('');

const passValidity = computed(() => {
	const validity = passwordValidity(password.value);
	if (validity) {
		return t(validity);
	}
});

const confirmPassValidity = computed(() => {
	if (confirmPassword.value && password.value !== confirmPassword.value) {
		return t('password_error_match');
	}
});

const canSubmit = computed(() => {
	if (needsCurrent.value && currentPassword.value.length === 0) {
		return false;
	}

	return password.value === confirmPassword.value;
});

async function submitConfirm() {
	try {
		const nextUser = await patchRequest<User>(`users/${user.id}`, {
			current_password: currentPassword.value,
			password: password.value
		});

		if (isMe.value) {
			Object.assign(user, nextUser);
			setUser(nextUser);
		}

		currentPassword.value = '';
		password.value = '';
		confirmPassword.value = '';

		showModal.value = false;
	} catch (error) {
		showError(error);
	}
}
</script>
