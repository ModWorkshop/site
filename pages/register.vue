<template>
    <page-block class="!w-1/3">
        <a-form @submit="register">
            <flex column gap="3">
                <a-input v-model="user.name" label="Name"/>
                <a-input v-model="user.email" label="Email"/>
                <flex>
                    <a-input v-model="user.current_password" label="Password" type="password"/>
                    <a-input v-model="user.password_confirm" label="Confirm Password" type="password" @input="checkConfirm"/>
                </flex>
                <flex column gap="2">
                    Or register using one the following
                    <flex>
                        <a-button href="http://localhost:8000/auth/steam/redirect" :icon="['fab', 'steam']" icon-size="lg"/>
                        <a-button :icon="['fab', 'google']" icon-size="lg"/>
                        <a-button :icon="['fab', 'twitter']" icon-size="lg"/>
                    </flex>
                </flex>
                <va-alert v-if="error" color="danger" class="w-full">{{error}}</va-alert>
                <div>
                    <a-button type="submit" large>{{$t('register')}}</a-button>
                </div>
            </flex>
        </a-form>    
    </page-block>
</template>

<script setup>
import { useStore } from '../store';

definePageMeta({
    middleware: 'guests-only'
});

const user = reactive({
    name: '',
    email: '',
    current_password: '',
    password_confirm: '',
});

const error = ref('');

const store = useStore();

async function register() {
    if (user.password_confirm !== user.current_password) {
        return;
    }
    error.value = '';
    try {
        await usePost('/register', user);
    } catch (error) {
        error.value = 'Something went wrong';
        console.log(error);
        return;
    }

    store.attemptLoginUser();
}

function checkConfirm() {
    if (user.password_confirm !== user.current_password) {
        error.value = "Passwords must match!";
    } else {
        error.value = '';
    }
}
</script>