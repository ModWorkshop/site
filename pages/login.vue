<template>
    <page-block class="!w-1/3">
        <a-form @submit="login">
            <flex column gap="3">
                <a-input label="Email" v-model="user.email"/>
                <a-input label="Password" type="password" v-model="user.current_password"/>
                <flex column gap="2">
                    Or register using one the following
                    <flex>
                        <a-button href="http://localhost:8000/auth/steam/redirect" :icon="['fab', 'steam']" icon-size="lg"/>
                        <a-button :icon="['fab', 'google']" icon-size="lg"/>
                        <a-button :icon="['fab', 'twitter']" icon-size="lg"/>
                    </flex>
                </flex>
                <a-input label="Remember Me" type="checkbox" v-model="user.remember"/>
                <div>
                    <a-button type="submit" large>{{$t('login')}}</a-button>
                </div>
            </flex>
        </a-form>    
    </page-block>
</template>

<script setup>
import { useStore } from '~~/store';

definePageMeta({
    middleware: 'guests-only'
});

const user = ref({
    email: '',
    current_password: '',
    remember: true,
});

const error = ref('');

const store = useStore();

async function login() {
    error.value = '';
    try {
        await usePost('/login', user.value);
    } catch (error) {
        const codes = {
            401: 'Incorrect email or password',
            422: 'Given email or password are invalid'
        };
        console.log(error);
        //error.value = codes[error.response.status] || 'Something went wrong';
        return;
    }

    store.attemptLoginUser(true);
}
</script>