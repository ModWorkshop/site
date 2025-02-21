<template>
    <page-block size="2xs">
        <Title>{{$t('register')}}</Title>
        <m-form autocomplete="off" @submit="register">
            <h1>{{$t('register')}}</h1>
            <m-content-block column gap="3" class="p-4">
                <m-img-uploader v-model="avatarBlob" :label="$t('avatar')" :max-file-size="store.settings?.image_max_file_size">
                    <template #image="{ src }">
                        <m-avatar size="xl" :src="src"/>
                        <m-avatar size="lg" :src="src"/>
                        <m-avatar size="md" :src="src"/>
                    </template>
                </m-img-uploader>
                <m-input v-model="user.name" required autocomplete="off" :label="$t('display_name')"/>
                <m-input v-model="user.unique_name" maxlength="30" required autocomplete="off" :label="$t('unique_name')"/>
                <m-input v-model="user.email" required autocomplete="off" maxlength="255" :label="$t('email')" type="email"/>
                <m-input v-model="user.my_extra_name" label="My Extra Name" class="hidden"/>
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
                <a-captcha v-model="captchaToken"/>
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
const store = useStore();
const showErrorToast = useQuickErrorToast();
const toaster = useToaster();
const router = useRouter();

const user = reactive({
    name: '',
    unique_name: '',
    email: '',
    password: '',
    password_confirm: '',
    my_extra_name: '' // Attention: This is a honeypot against bots
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
const captchaToken = ref<string>('');
const canRegister = computed(() => user.name && user.email && user.unique_name && user.password && user.password_confirm && captchaToken.value);

async function register() {
    if (user.password_confirm !== user.password) {
        return;
    }

    try {
        loading.value = true;
        await postRequest('/register', serializeObject({
            ...user,
            avatar_file: avatarBlob.value,
            'h-captcha-response': captchaToken.value
        }));

        router.push('/');
        toaster.showToast({
            title: t('new_user_title'),
            desc: t('new_user_desc'),
            duration: 15000,
            color: 'success'
        })
  
        // store.attemptLoginUser();
        // reloadToken();
    } catch (e) {
        showErrorToast(e, {
            409: t('register_error_409'),
        });

        loading.value = false;
    }
    captchaToken.value = '';
}
</script>