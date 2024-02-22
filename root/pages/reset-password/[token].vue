<template>
    <page-block size="2xs">
        <Title>{{$t('reset_password')}}</Title>
        <m-form @submit="reset">
            <h1>{{$t('reset_password')}}</h1>
            <m-content-block column gap="3">
                <m-flex column>
                    <m-input v-if="!user" v-model="email" :disabled="sent" :label="$t('email')" type="email"/>
                    <m-flex>
                        <m-input 
                            v-model="password"
                            required
                            autocomplete="off"
                            :validity="passValidity"
                            minlength="12"
                            maxlength="128"
                            :label="$t('password')" 
                            type="password"
                        />
                        <m-input 
                            v-model="passwordConfirm"
                            required
                            autocomplete="off"
                            :validity="confirmPassValidity"
                            minlength="12"
                            maxlength="128"
                            :label="$t('confirm_password')" 
                            type="password"
                        />
                    </m-flex>
                    <small>{{$t('password_guide')}}</small>
                </m-flex>
                <div>
                    <m-button type="submit" :disabled="sending || (!email && !user) || (!!password && passwordConfirm !== password)">{{$t('submit')}}</m-button>
                </div>
            </m-content-block>
        </m-form>    
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';

const { t } = useI18n();
const route = useRoute();

const { user } = useStore();

const password = ref('');
const email = ref('');
const passwordConfirm = ref('');
const sent = ref(false);
const sending = ref(false);
const showError = useQuickErrorToast();

const passValidity = computed(() => {
    const validity = passwordValidity(password.value);
    if (validity) {
        return t(validity);
    }
});

const confirmPassValidity = computed(() => {
    if (passwordConfirm.value && password.value !== passwordConfirm.value) {
        return t('password_error_match');
    }
});


async function reset() {
    sending.value = true;
    try {
        await postRequest('reset-password', {
            email: user ? user.email : email.value,
            password: password.value,
            token: route.params.token
        });
    } catch (error) {
        showError(error);
        sending.value = false;
    }
}
</script>