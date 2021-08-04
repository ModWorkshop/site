<template>
    <el-col :span="8" :offset="8">
        <el-form @submit.native.prevent="register">
            <el-form-item label="Name">
                <el-input v-model="name"/>
            </el-form-item>
            <el-form-item label="Email">
                <el-input v-model="email"/>
            </el-form-item>
            <el-form-item label="Password">
                <el-input type="password" v-model="password"/>
            </el-form-item>
            <el-form-item label="Confirm Password">
                <el-input type="password" v-model="passwordConfirm" @input="checkConfirm"/>
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
    data() {
        return {
            name: '',
            email: '',
            password: '',
            passwordConfirm: '',
            error: false
        }
    },
    middleware({ store, redirect }) {
        if (store.state.user) {
            redirect('/');
        }
    },
    methods: {
        async register() {
            if (this.passwordConfirm !== this.password) {
                return;
            }
            this.error = false;
            try {
                await this.$axios.post('/register', {name: this.name, email: this.email, password: this.password});
            } catch (error) {
                this.error = 'Something went wrong';
                console.log(error);
                return;
            }

            const user = await this.$axios.get('/user').then(res => res.data);
            this.$store.commit('setUser', user);
            this.$router.push('/');
        },
        checkConfirm() {
            if (this.passwordConfirm !== this.password) {
                this.error = "Passwords must match!";
            } else {
                this.error = false;
            }
        }
    }
}
</script>