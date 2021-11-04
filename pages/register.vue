<template>
    <el-col :span="8" :offset="8">
        <el-form @submit.native.prevent="register">
            <el-form-item label="Name">
                <el-input v-model="user.name"/>
            </el-form-item>
            <el-form-item label="Email">
                <el-input v-model="user.email"/>
            </el-form-item>
            <el-form-item label="Password">
                <el-input type="password" v-model="user.password"/>
            </el-form-item>
            <el-form-item label="Confirm Password">
                <el-input type="password" v-model="user.passwordConfirm" @input="user.checkConfirm"/>
            </el-form-item>
            <el-form-item>
                <span v-if="error">{{error}}</span>
                <el-input type="submit" value="Register"/>
            </el-form-item>
        </el-form>        
    </el-col>
</template>

<script>
export default {
    middleware: 'guests-only'
};
</script>

<script setup>
import { useStore, reactive, ref, useRouter, useContext } from '~~/store';

const user = reactive({
    name: '',
    email: '',
    password: '',
    passwordConfirm: '',
});

const error = ref('');

const { $axios } = useContext();
const store = useStore();
const router = useRouter();

async function register() {
    if (user.passwordConfirm !== user.password) {
        return;
    }
    error.value = '';
    try {
        await $axios.post('/register', user);
    } catch (error) {
        error.value = 'Something went wrong';
        console.log(error);
        return;
    }

    const { data: user } = await $axios.get('/user');
    store.user = user;
    router.push('/');
}

function checkConfirm() {
    if (user.passwordConfirm !== user.password) {
        error.value = "Passwords must match!";
    } else {
        error.value = '';
    }
}
</script>