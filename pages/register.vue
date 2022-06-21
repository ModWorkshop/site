<template>
    <page-block class="!w-1/3">
        <a-form @submit="register">
            <flex column gap="3">
                <group label="Name">
                    <a-input v-model="user.name"/>
                </group>
                <group label="Email">
                    <a-input v-model="user.email"/>
                </group>
                <group label="Password">
                    <a-input type="password" v-model="user.current_password"/>
                </group>
                <group label="Confirm Password">
                    <a-input type="password" v-model="user.password_confirm" @input="checkConfirm"/>
                </group>
                <group label="Or register using one the following" gap="2">
                    <a-button href="http://localhost:8000/auth/steam/redirect" :icon="['fab', 'steam']" icon-size="lg"/>
                    <a-button :icon="['fab', 'google']" icon-size="lg"/>
                    <a-button :icon="['fab', 'twitter']" icon-size="lg"/>
                </group>
                <group>
                    <span v-if="error">{{error}}</span>
                    <a-button type="submit" large>{{$t('register')}}</a-button>
                </group>
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
    password: '',
    password_confirm: '',
});

const error = ref('');

const store = useStore();

async function register() {
    if (user.password_confirm !== user.password) {
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
    if (user.passwordConfirm !== user.password) {
        error.value = "Passwords must match!";
    } else {
        error.value = '';
    }
}
</script>