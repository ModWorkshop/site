<template>
    <page-block class="!w-1/2">
        <Title>{{$t('register')}}</Title>
        <a-form autocomplete="off" @submit="register">
            <h1>{{$t('register')}}</h1>
            <content-block column gap="3" class="p-4">
                <img-uploader v-model="avatarBlob" label="Avatar">
                    <template #label="{ src }">
                        <a-avatar size="xl" :src="src"/>
                        <a-avatar size="lg" :src="src"/>
                        <a-avatar size="md" :src="src"/>
                    </template>
                </img-uploader>
                <a-input v-model="user.name" required autocomplete="off" :label="$t('display_name')"/>
                <a-input v-model="user.unique_name" required autocomplete="off" :label="$t('unique_name')"/>
                <a-input v-model="user.email" required autocomplete="off" :label="$t('email')" type="email"/>
                <flex column>
                    <flex>
                        <a-input 
                            v-model="user.password"
                            required
                            autocomplete="off"
                            :validity="passValidity"
                            minlength="12"
                            maxlength="128"
                            :label="$t('password')" 
                            type="password"
                        />
                        <a-input 
                            v-model="user.password_confirm"
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
                <flex column gap="2">
                    {{$t('login_using_services')}}
                    <the-social-logins/>
                </flex>
                <div>
                    <a-button type="submit" :disabled="!canRegister">{{$t('register')}}</a-button>
                </div>
            </content-block>
        </a-form>    
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '../store';

definePageMeta({
    middleware: 'guests-only'
});

const { t } = useI18n();
const showErrorToast = useQuickErrorToast();

const user = reactive({
    name: '',
    unique_name: '',
    email: '',
    password: '',
    password_confirm: '',
});

const avatarBlob = ref(null);

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

const canRegister = computed(() => user.name && user.email && user.unique_name && user.password && user.password_confirm);

const store = useStore();

async function register() {
    if (user.password_confirm !== user.password) {
        return;
    }

    try {
        await usePost('/register', serializeObject({...user, avatar_file: avatarBlob.value}));  
        Object.assign(user, {
            name: '',
            unique_name: '',
            email: '',
            password: '',
            password_confirm: '',
        });
        store.attemptLoginUser();
    } catch (e) {
        showErrorToast(e, {
            409: t('register_error_409'),
        });
    }
}
</script>