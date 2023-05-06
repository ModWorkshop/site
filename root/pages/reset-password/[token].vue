<template>
    <page-block size="2xs">
        <Title>{{$t('reset_password')}}</Title>
        <a-form @submit="reset">
            <h1>{{$t('reset_password')}}</h1>
            <content-block column gap="3">
                <flex column>
                    <a-input v-if="!user" v-model="email" :disabled="sent" :label="$t('email')" type="email"/>
                    <flex>
                        <a-input 
                            v-model="password"
                            required
                            autocomplete="off"
                            :validity="passValidity"
                            minlength="12"
                            maxlength="128"
                            :label="$t('password')" 
                            type="password"
                        />
                        <a-input 
                            v-model="passwordConfirm"
                            required
                            autocomplete="off"
                            :validity="confirmPassValidity"
                            minlength="12"
                            maxlength="128"
                            :label="$t('confirm_password')" 
                            type="password"
                        />
                    </flex>
                    <small>{{$t('password_guide')}}</small>
                </flex>
                <div>
                    <a-button type="submit" :disabled="sending || (!email && !user) || (!!password && passwordConfirm !== password)">{{$t('submit')}}</a-button>
                </div>
            </content-block>
        </a-form>    
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
    await usePost('reset-password', {
        email: user ? user.email : email.value,
        password: password.value,
        token: route.params.token
    });
    sent.value = true;
}
</script>