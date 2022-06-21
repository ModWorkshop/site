<template>
    <page-block class="!w-1/3">
        <a-form @submit="login">
            <flex column gap="3">
                <group label="Email">
                    <a-input v-model="user.email"/>
                </group>
                <group label="Password">
                    <a-input type="password" v-model="user.current_password"/>
                </group>
                <group label="Or login using one the following" gap="2">
                    <a-button href="http://localhost:8000/auth/steam/redirect" :icon="['fab', 'steam']" icon-size="lg"/>
                    <a-button :icon="['fab', 'google']" icon-size="lg"/>
                    <a-button :icon="['fab', 'twitter']" icon-size="lg"/>
                </group>
                <group>
                    <a-input type="checkbox" label="Remember Me" v-model="user.remember"/>
                </group>
                <group>
                    <a-button type="submit" large>{{$t('login')}}</a-button>
                </group>
            </flex>
        </a-form>    
    </page-block>
</template>

<script setup>
import { useStore } from '~~/store';
import { reloadCSRF } from '~~/utils/helpers';

definePageMeta({
    middleware: 'guests-only'
});

const user = ref({
    email: '',
    current_password: '',
    remember: true,
});

const error = ref('');

const { $ftch } = useNuxtApp();
const store = useStore();
const router = useRouter();

async function login() {
    error.value = '';
    try {
        await $ftch('/login', { method: "POST", body: user.value });
    } catch (error) {
        const codes = {
            401: 'Incorrect email or password',
            422: 'Given email or password are invalid'
        };
        console.log(error);
        //error.value = codes[error.response.status] || 'Something went wrong';
        return;
    }

    const userData = await $ftch('/user');
    console.log(userData);
    store.user = userData;
    await reloadCSRF();
    router.push('/');
}
</script>