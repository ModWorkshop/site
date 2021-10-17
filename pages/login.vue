<template>
    <page-block style="max-width: 50%;">
        <el-form @submit.native.prevent="login">
            <group label="Email">
                <el-input v-model="user.email"/>
            </group>
            <group label="Passowrd">
                <el-input type="password" v-model="user.password"/>
            </group>
            <group>
                <el-checkbox label="Remember Me" v-model="user.remember"/>
            </group>
            <group>
                <span v-if="error">{{error}}</span>
                <el-input type="submit" value="Login"/>
            </group>
        </el-form>    
    </page-block>
</template>

<script>
export default {
    middleware: 'guests-only'
};
</script>

<script setup>
import { useStore } from '~~/store';

const user = ref({
    email: '',
    password: '',
    remember: true,
});

const error = ref('');

const { $axios } = useNuxtApp().legacyApp;
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