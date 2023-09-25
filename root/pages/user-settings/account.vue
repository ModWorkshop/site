<template>
    <flex gap="2" column>
        <a-alert v-if="isMe && !user.signable" color="warning" :title="$t('sso_only_warning')" :desc="$t('sso_only_warning_desc')"/>
        <a-input v-model="user.unique_name" :label="$t('unique_name')" :desc="$t('unique_name_desc')"/>
        <a-input v-model="user.email" maxlength="255" :label="$t('email')"/>
        <flex class="items-center my-3">
            <h3>{{$t('change_password')}} </h3>
            <a-button class="ml-auto" @click="changePassword = !changePassword">{{$t(changePassword ? 'cancel' : 'edit')}}</a-button>
        </flex>
        <template v-if="changePassword">
            <flex>
                <a-input 
                    v-if="isMe && user.signable"
                    v-model="user.current_password"
                    autocomplete="off"
                    :label="$t('current_password')"
                    type="password"
                    minlength="12"
                    maxlength="128"
                />
                <a-input 
                    v-model="user.password"
                    :validity="passValidity"
                    autocomplete="off"
                    :label="$t('new_password')"
                    type="password"
                    minlength="12"
                    maxlength="128"
                />
                <a-input
                    v-model="user.confirm_password"
                    :validity="confirmPassValidity"
                    :label="$t('confirm_password')"
                    type="password"
                    minlength="12"
                    maxlength="128"
                />
            </flex>
            <small>{{$t('password_guide')}}</small>
            <a-link-button v-if="user.signable" disabled @click="reset">{{$t('forgot_password_button')}}</a-link-button>
        </template>
        <a-alert v-if="isMe" color="info" :title="$t('request_my_data')">
            {{$t('request_my_data_desc')}}
            <a ref="downloadDataButton" download :href="`${config.apiUrl}/user-data`"/>
            <div>
                <a-button @click="downloadData">{{$t('download')}}</a-button>
            </div>
        </a-alert>

        <a-alert class="w-full" color="danger" :title="$t('danger_zone')">
            <div>
                <a-button color="danger" @click="showDeleteUser">{{$t('delete')}}</a-button>
            </div>
        </a-alert>

        <a-modal-form v-model="showDeletUser" :title="$t('delete_user')" :desc="$t('delete_user_desc')" :can-submit="canDeleteUser" @submit="doDelete">
            <a-alert color="danger">
                {{$t('delete_user_warn')}}
            </a-alert>
            <a-input v-model="deleteUserUniqueName" :label="$t('unique_name')"/>
            <a-input v-model="deleteUserCheckBox" :label="$t('delete_user_checkbox')" type="checkbox"/>
            <NuxtTurnstile v-model="turnstileToken"/>
        </a-modal-form>
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { UserForm } from '~~/types/models';
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
const turnstileToken = ref('');
const canDeleteUser = computed(() => 
    deleteUserCheckBox.value && turnstileToken.value && deleteUserUniqueName.value === me?.unique_name ? true : false
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
    turnstileToken.value = '';
    deleteUserUniqueName.value = '';
    deleteUserCheckBox.value = false;
}

async function doDelete() {
    try {
        await deleteRequest(`users/${props.user.id}`, {
            'cf-turnstile-response': turnstileToken.value,
            'are_you_sure': deleteUserCheckBox.value,
            'unique_name': deleteUserUniqueName.value
        });
        if (me!.id === props.user.id) {
            await store.logout();
        }
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