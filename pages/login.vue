<template>
    <el-col :span="8">
        <el-form @submit.native.prevent="login">
            <el-form-item label="Email">
                <el-input v-model="email"/>
            </el-form-item>
            <el-form-item label="Passowrd">
                <el-input type="password" v-model="password"/>
            </el-form-item>
            <el-form-item>
                <el-checkbox label="Remember Me" v-model="remember"/>
            </el-form-item>
            <el-form-item>
                <span v-if="error">{{error}}</span>
                <el-input type="submit" value="Login"/>
            </el-form-item>
        </el-form>        
    </el-col>
</template>
<script>
export default {
    data() {
        return {
            email: '',
            password: '',
            remember: true,
            error: false
        }
    },
    middleware({ store, redirect }) {
        if (store.state.user) {
            redirect('/');
        }
    },
    methods: {
        async login() {
            this.error = false;
            try {
                await this.$axios.post('/login', {email: this.email, password: this.password, remember_me: this.remember});
            } catch (error) {
                const codes = {
                    401: 'Incorrect email or password',
                    422: 'Given email or password are invalid'
                }
                console.log(error.response.status);
                this.error = codes[error.response.status] || 'Something went wrong';
                return;
            }

            const user = await this.$axios.get('/user').then(res => res.data);
            this.$store.commit('setUser', user);
            this.$router.push('/');
        }
    }
}
</script>