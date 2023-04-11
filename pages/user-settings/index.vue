<template>
    <flex gap="2" column>
        <a-alert v-if="!user.signable" color="warning" :title="$t('sso_only_warning')" :desc="$t('sso_only_warning_desc')"/>
        <a-input v-model="user.name" :label="$t('display_name')" maxlength="30"/>
        <a-input v-model="user.unique_name" :label="$t('unique_name')" :desc="$t('unique_name_desc')"/>
        <a-input v-if="user.email || isMe" v-model="user.email" maxlength="255" :label="$t('email')" :disabled="!isMe"/>
        <template v-if="isMe">
            <h3>{{$t('change_password')}}</h3>
            <flex>
                <a-input 
                    v-if="user.signable"
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
            <a-link-button disabled @click="reset">{{$t('forgot_password_button')}}</a-link-button>
        </template>
    
        <a-alert color="info" :title="$t('request_my_data')">
            {{$t('request_my_data_desc')}}
            <a ref="downloadDataButton" download :href="`${config.apiUrl}/user-data`"/>
            <div>
                <a-button @click="downloadData">{{$t('download')}}</a-button>
            </div>
        </a-alert>

        <a-alert class="w-full" color="danger" :title="$t('danger_zone')">
            <div>
                <a-button color="danger" @click="doDelete">{{$t('delete')}}</a-button>
            </div>
        </a-alert>
    </flex>
</template>

<script setup lang="ts">
import { Ref } from 'vue';
import { useStore } from '~~/store';
import { UserForm } from '~~/types/models';
import { useI18n } from 'vue-i18n';
import { passwordValidity } from '~~/utils/helpers';

const props = defineProps<{
    user: UserForm
}>();

const { public: config } = useRuntimeConfig();

const { t } = useI18n();
const { user: me } = useStore();

const store = useStore();

const { showToast } = useToaster();

const downloadDataButton = ref<HTMLAnchorElement>();

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

const yesNoModal = useYesNoModal();
const isMe = inject<Ref<boolean>>('isMe');

async function doDelete() {
    yesNoModal({
        title: t('are_you_sure'),
        desc: t('irreversible_action'),
        async yes() {
            await useDelete(`users/${props.user.id}`);
            await store.logout();
        }
    });
}

async function reset() {
    if (me) {
        await usePost('forgot-password', {
            email: me.email
        });

        showToast({
            color: 'success',
            desc: t('password_reset_sent_unknown')
        });
    }
}
</script>