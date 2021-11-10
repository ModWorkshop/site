<template>
    <page-block class="!w-1/3">
        <a-form @submit="login">
            <flex column gap="3">
                <group label="Email">
                    <el-input v-model="user.email"/>
                </group>
                <group label="Password">
                    <el-input type="password" v-model="user.password"/>
                </group>
                <group label="Or login using one the following" gap="2">
                    <a-button href="http://localhost:8000/auth/steam/redirect" :icon="['fab', 'steam']" icon-size="lg"/>
                    <a-button :icon="['fab', 'google']" icon-size="lg"/>
                    <a-button :icon="['fab', 'twitter']" icon-size="lg"/>
                </group>
                <group>
                    <el-checkbox label="Remember Me" v-model="user.remember"/>
                </group>
                <group>
                    <a-button type="submit" large>{{$t('login')}}</a-button>
                </group>
            </flex>
        </a-form>    
    </page-block>
</template>

<script>
export default {
    middleware: 'guests-only'
};
</script>

<script setup>
import { ref, useRouter, useContext } from '@nuxtjs/composition-api';

import { useStore } from '~~/store';

const user = ref({
    email: '',
    password: '',
    remember: true,
});

const error = ref('');

const { $axios } = useContext();
const store = useStore();
const router = useRouter();

async function login() {
    error.value = '';
    try {
        await $axios.post('/login', user.value);
    } catch (error) {
        const codes = {
            401: 'Incorrect email or password',
            422: 'Given email or password are invalid'
        };
        console.log(error);
        //error.value = codes[error.response.status] || 'Something went wrong';
        return;
    }

    const { data: userData } = await $axios.get('/user');
    store.user = userData;
    router.push('/');
}
</script>