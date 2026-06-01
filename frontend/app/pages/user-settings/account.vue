<template>
	<m-form v-model="userForm" float-save-gui autocomplete="off" :flush-changes="fc" @submit="save">
		<m-flex gap="2" column>
			<m-alert v-if="isMe && !user.signable" color="warning" :title="$t('sso_only_warning')" :desc="$t('sso_only_warning_desc')"/>
			<m-input
				v-if="hasPermission('manage-users')"
				v-model="userForm.purged_user"
				label="Purged User"
				type="checkbox"
				desc="This option is generally used with the Purge User option as a tool against bots. It essentailly hides the user from appearing on sitemap and such.."
			/>
			<m-input v-model="userForm.unique_name" :label="$t('unique_name')" :desc="$t('unique_name_desc')"/>
			<m-input v-model="userForm.email" maxlength="255" :label="$t('email')"/>
			<m-flex class="items-center my-3">
				<label>{{ $t('change_password') }}</label>
				<m-button class="ml-auto" @click="changePassword = !changePassword">{{ $t(changePassword ? 'cancel' : 'edit') }}</m-button>
			</m-flex>
			<template v-if="changePassword">
				<m-flex>
					<m-input
						v-if="isMe && user.signable"
						v-model="userForm.current_password"
						autocomplete="off"
						:label="$t('current_password')"
						type="password"
						minlength="12"
						maxlength="128"
					/>
					<m-input
						v-model="userForm.password"
						:validity="passValidity"
						autocomplete="off"
						:label="$t('new_password')"
						type="password"
						minlength="12"
						maxlength="128"
					/>
					<m-input
						v-model="userForm.confirm_password"
						:validity="confirmPassValidity"
						:label="$t('confirm_password')"
						type="password"
						minlength="12"
						maxlength="128"
					/>
				</m-flex>
				<small>{{ $t('password_guide') }}</small>
				<m-link v-if="user.signable" disabled @click="reset">{{ $t('forgot_password_button') }}</m-link>
			</template>
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
	password: '',
	current_password: '',
	confirm_password: '',
	purged_user: user.purged_user
});

const { public: config } = useRuntimeConfig();

const fc = createEventHook();
const { t } = useI18n();
const changePassword = ref(false);
const { user: me, setUser, logout, hasPermission } = useStore();

const { showToast } = useToaster();
const showError = useQuickErrorToast();

const downloadDataButton = ref<HTMLAnchorElement>();
const showDeletUser = ref(false);

const deleteUserUniqueName = ref('');
const deleteUserCheckBox = ref(false);
const captchaToken = ref('-');
const canDeleteUser = computed(() =>
	deleteUserCheckBox.value && captchaToken.value && deleteUserUniqueName.value === userForm?.unique_name ? true : false
);

function downloadData() {
	if (downloadDataButton.value) {
		downloadDataButton.value.click();
	}
}

const passValidity = computed(() => {
	const validity = passwordValidity(userForm.password);
	if (validity) {
		return t(validity);
	}
});

const confirmPassValidity = computed(() => {
	if (userForm.confirm_password && userForm.password !== userForm.confirm_password) {
		return t('password_error_match');
	}
});

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
		}

		userForm.password = '';
		userForm.confirm_password = '';
		userForm.current_password = '';

		fc.trigger(userForm);
	} catch (error) {
		showError(error);
	}
}

async function reset() {
	if (me) {
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
