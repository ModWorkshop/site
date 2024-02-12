<template>
    <page-block size="2xs">
        <Title>{{$t('register')}}</Title>
        <m-form autocomplete="off" @submit="register">
            <h1>{{$t('register')}}</h1>
            <m-content-block column gap="3" class="p-4">
                <m-img-uploader v-model="avatarBlob" :label="$t('avatar')" :max-file-size="settings?.image_max_file_size">
                    <template #label="{ src }">
                        <m-avatar size="xl" :src="src"/>
                        <m-avatar size="lg" :src="src"/>
                        <m-avatar size="md" :src="src"/>
                    </template>
                </m-img-uploader>
                <m-input v-model="user.name" required autocomplete="off" :label="$t('display_name')"/>
                <m-input v-model="user.unique_name" maxlength="30" required autocomplete="off" :label="$t('unique_name')"/>
                <m-input v-model="user.email" required autocomplete="off" maxlength="255" :label="$t('email')" type="email"/>
                <m-flex column>
                    <m-flex>
                        <m-input 
                            v-model="user.password"
                            required
                            autocomplete="off"
                            :validity="passValidity"
                            minlength="12"
                            maxlength="128"
                            :label="$t('password')" 
                            type="password"
                        />
                        <m-input 
                            v-model="user.password_confirm"
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
                    <m-button type="submit" :loading="loading" :disabled="!canRegister">{{$t('register')}}</m-button>
                </div>

                <NuxtLink class="mt-2" to="login">{{$t('already_have_account')}}</NuxtLink>
                <m-flex column>
                    {{$t('login_using_services')}}
                    <the-social-logins/>
                </m-flex>
                <NuxtTurnstile ref="turnstile" v-model="turnstileToken"/>
            </m-content-block>
        </m-form>    
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { passwordValidity, serializeObject } from '~~/utils/helpers';
import { useStore } from '../store';

definePageMeta({
    middleware: 'guests-only'
});

const { t } = useI18n();
const { settings } = useStore();
const showErrorToast = useQuickErrorToast();

const user = reactive({
    name: '',
    unique_name: '',
    email: '',
    password: '',
    password_confirm: '',
});

const avatarBlob = ref();

const passValidity = computed(() => {
    const validity = passwordValidity(user.password);
    if (validity) {
        return t(validity);
    }
});

const confirmPassValidity = computed(() => {
    if (user.password_confirm && user.password !== user.password_confirm) {
        return t('password_error_match');
    }
});

const loading = ref(false);
const canRegister = computed(() => user.name && user.email && user.unique_name && user.password && user.password_confirm && turnstileToken.value);

const turnstile = ref();
const turnstileToken = ref<string>();

const store = useStore();

async function register() {
    if (user.password_confirm !== user.password) {
        return;
    }

    const token = turnstileToken.value;
    turnstileToken.value = '';

    try {
        loading.value = true;
        await postRequest('/register', serializeObject({
            ...user,
            avatar_file: avatarBlob.value,
            'cf-turnstile-response': token
        }));  
        store.attemptLoginUser();
        reloadToken();
    } catch (e) {
        showErrorToast(e, {
            409: t('register_error_409'),
        });

        loading.value = false;

        turnstile.value?.reset();
    }
}
</script>