<template>
    <m-flex gap="2" column>
        <m-alert v-if="isMe && !user.signable" color="warning" :title="$t('sso_only_warning')" :desc="$t('sso_only_warning_desc')"/>
        <m-input v-model="user.unique_name" :label="$t('unique_name')" :desc="$t('unique_name_desc')"/>
        <m-input v-model="user.email" maxlength="255" :label="$t('email')"/>
        <m-flex class="items-center my-3">
            <label>{{$t('change_password')}}</label>
            <m-button class="ml-auto" @click="changePassword = !changePassword">{{$t(changePassword ? 'cancel' : 'edit')}}</m-button>
        </m-flex>
        <template v-if="changePassword">
            <m-flex>
                <m-input 
                    v-if="isMe && user.signable"
                    v-model="user.current_password"
                    autocomplete="off"
                    :label="$t('current_password')"
                    type="password"
                    minlength="12"
                    maxlength="128"
                />
                <m-input 
                    v-model="user.password"
                    :validity="passValidity"
                    autocomplete="off"
                    :label="$t('new_password')"
                    type="password"
                    minlength="12"
                    maxlength="128"
                />
                <m-input
                    v-model="user.confirm_password"
                    :validity="confirmPassValidity"
                    :label="$t('confirm_password')"
                    type="password"
                    minlength="12"
                    maxlength="128"
                />
            </m-flex>
            <small>{{$t('password_guide')}}</small>
            <m-link v-if="user.signable" disabled @click="reset">{{$t('forgot_password_button')}}</m-link>
        </template>
        <m-alert v-if="isMe" color="info" :title="$t('request_my_data')">
            {{$t('request_my_data_desc')}}
            <a ref="downloadDataButton" download :href="`${config.apiUrl}/user-data`"/>
            <div>
                <m-button @click="downloadData">{{$t('download')}}</m-button>
            </div>
        </m-alert>

        <m-alert class="w-full" color="danger" :title="$t('danger_zone')">
            <div>
                <m-button color="danger" @click="showDeleteUser">{{$t('delete')}}</m-button>
            </div>
        </m-alert>

        <m-form-modal v-model="showDeletUser" :title="$t('delete_user')" :desc="$t('delete_user_desc')" :can-submit="canDeleteUser" @submit="doDelete">
            <m-alert color="danger">
                {{$t('delete_user_warn')}}
            </m-alert>
            <m-input v-model="deleteUserUniqueName" :label="$t('unique_name')"/>
            <m-input v-model="deleteUserCheckBox" :label="$t('delete_user_checkbox')" type="checkbox"/>
            <a-captcha v-model="captchaToken"/>
        </m-form-modal>
    </m-flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import type { UserForm } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { passwordValidity } from '~~/utils/helpers';

const props = defineProps<{
    user: UserForm
}>();

const { public: config } = useRuntimeConfig();

const { t } = useI18n();
const changePassword = ref(false);
const store = useStore();
const { user: me } = store;

const { showToast } = useToaster();
const showError = useQuickErrorToast();

const downloadDataButton = ref<HTMLAnchorElement>();
const showDeletUser = ref(false);

const deleteUserUniqueName = ref('');
const deleteUserCheckBox = ref(false);
const captchaToken = ref('-');
const canDeleteUser = computed(() => 
    deleteUserCheckBox.value && captchaToken.value && deleteUserUniqueName.value === props.user?.unique_name ? true : false
);

function downloadData() {
    if (downloadDataButton.value) {
        downloadDataButton.value.click();
    }
}

const passValidity = computed(() => {
    const validity = passwordValidity(props.user.password);
    if (validity) {
        return t(validity);
    }
});

const confirmPassValidity = computed(() => {
    if (props.user.confirm_password && props.user.password !== props.user.confirm_password) {
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
        await deleteRequest(`users/${props.user.id}`, {
            'h-captcha-response': captchaToken.value,
            'are_you_sure': deleteUserCheckBox.value,
            'unique_name': deleteUserUniqueName.value
        });
        if (me!.id === props.user.id) {
            await store.logout();
        } else {
            await navigateTo(""); // Return home   
        }
    } catch (error) {
        showError(error);
    }
    captchaToken.value = '';
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