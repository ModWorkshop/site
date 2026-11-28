<template>
	<m-form v-model="userForm" float-save-gui autocomplete="off" :flush-changes="fc" @submit="save">
		<m-flex gap="2" column>
			<m-alert v-if="isMe && !user.signable" color="warning" :title="$t('sso_only_warning')" :desc="$t('sso_only_warning_desc')"/>
			<h2>{{ $t('account_tab') }}</h2>
			<m-input
				v-if="hasPermission('manage-users')"
				v-model="userForm.purged_user"
				label="Purged User"
				type="checkbox"
				desc="This option is generally used with the Purge User option as a tool against bots. It essentailly hides the user from appearing on sitemap and such.."
			/>
			<m-input v-model="userForm.unique_name" :label="$t('unique_name')" :desc="$t('unique_name_desc')"/>
			<m-input v-model="userForm.email" maxlength="255" :label="$t('email')"/>
			<div class="dropdown-splitter"/>
			<h2>{{ $t('password_and_security') }}</h2>
			<m-flex class="items-center my-3">
				<label>{{ $t('change_password') }}</label>
				<m-flex class="ml-auto">
					<m-button @click="showChangePasswordModal = true">{{ $t('edit') }}</m-button>
					<m-button v-if="user.has_password" :disabled="disableResetPass" @click="reset">{{ $t('forgot_password_button') }}</m-button>
				</m-flex>
			</m-flex>
			<m-flex class="items-center my-3">
				<label>{{ $t('2fa') }}</label>
				<m-flex v-if="isMe" class="ml-auto">
					<template v-if="user.two_factor_confirmed_at">
						<m-button @click="showTwoFactorModal = 'disable'">{{ $t('disable_2fa') }}</m-button>
						<m-button @click="showTwoFactorModal = 'recovery-codes'">{{ $t('2fa_recovery_codes') }}</m-button>
					</template>
					<m-dropdown v-else type="tooltip" dropdown-class="p-2" :disabled="user.signable">
						<m-button :disabled="!user.signable" @click="showTwoFactorModal = 'enable'">{{ $t('enable_2fa') }}</m-button>
						<template #content>
							{{ $t('signable_only') }}
						</template>
					</m-dropdown>
				</m-flex>
				<h3 v-else-if="hasPermission('manage-users')" class="ml-auto">
					{{ user.two_factor_confirmed_at ? '✅' : '❎' }}
				</h3>
			</m-flex>
			<m-alert v-if="isMe" color="info" :title="$t('request_my_data')">
				{{ $t('request_my_data_desc') }}
				<a ref="downloadDataButton" download :href="`${config.apiUrl}/user-data`"/>
				<div>
					<m-button @click="downloadData">{{ $t('download') }}</m-button>
				</div>
			</m-alert>

			<m-alert class="w-full" color="danger" :title="$t('danger_zone')">
				<div>
					<m-button color="danger" @click="showDeleteUser">{{ $t('delete') }}</m-button>
				</div>
			</m-alert>

			<m-form-modal v-model="showDeletUser" :title="$t('delete_user')" :desc="$t('delete_user_desc')" :can-submit="canDeleteUser" @submit="doDelete">
				<m-alert color="danger">
					{{ $t('delete_user_warn') }}
				</m-alert>
				<m-input v-model="deleteUserUniqueName" :label="$t('unique_name')"/>
				<m-input v-model="deleteUserCheckBox" :label="$t('delete_user_checkbox')" type="checkbox"/>
				<a-captcha v-model="captchaToken"/>
			</m-form-modal>

			<two-factor-auth-modal v-model="showTwoFactorModal" :user="user"/>
			<change-password-modal v-model="showChangePasswordModal" :user="user"/>
		</m-flex>
	</m-form>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { User, UserForm } from '~/types/models';
import { useI18n } from 'vue-i18n';

const { user } = defineProps<{
	user: UserForm;
}>();

const userForm = reactive({
	unique_name: user.unique_name,
	email: user.email,
	purged_user: user.purged_user
});

const { public: config } = useRuntimeConfig();

const fc = createEventHook();
const { t } = useI18n();
const { user: me, setUser, logout, hasPermission } = useStore();

const { showToast } = useToaster();
const showError = useQuickErrorToast();

const downloadDataButton = ref<HTMLAnchorElement>();
const showDeletUser = ref(false);

const deleteUserUniqueName = ref('');
const deleteUserCheckBox = ref(false);
const captchaToken = ref('-');

const showTwoFactorModal = ref<'hidden' | 'enable' | 'disable' | 'recovery-codes'>('hidden');
const showChangePasswordModal = ref(false);
const disableResetPass = ref(false);

const canDeleteUser = computed(() =>
	deleteUserCheckBox.value && (!config.hcaptchaSiteKey || captchaToken.value) && deleteUserUniqueName.value === userForm?.unique_name ? true : false
);

function downloadData() {
	if (downloadDataButton.value) {
		downloadDataButton.value.click();
	}
}

const isMe = inject<boolean>('isMe');

function showDeleteUser() {
	showDeletUser.value = true;
	captchaToken.value = '';
	deleteUserUniqueName.value = '';
	deleteUserCheckBox.value = false;
}

async function doDelete() {
	try {
		await deleteRequest(`users/${user.id}`, {
			'h-captcha-response': captchaToken.value,
			'are_you_sure': deleteUserCheckBox.value,
			'unique_name': deleteUserUniqueName.value
		});
		if (me!.id === user.id) {
			await logout('/');
		} else {
			await navigateTo('/'); // Return home
		}
	} catch (error) {
		showError(error);
	}
	captchaToken.value = '';
}

async function save() {
	try {
		const nextUser = await patchRequest<User>(`users/${user.id}`, userForm);

		if (isMe) {
			setUser(nextUser);
			Object.assign(user, nextUser);
		}

		fc.trigger(userForm);
	} catch (error) {
		showError(error);
	}
}

async function reset() {
	if (me) {
		disableResetPass.value = true;

		setTimeout(() => {
			disableResetPass.value = false;
		}, 10000);

		await postRequest('forgot-password', {
			email: me.email
		});

		showToast({
			color: 'success',
			desc: t('password_reset_sent_unknown')
		});
	}
}

</script>
