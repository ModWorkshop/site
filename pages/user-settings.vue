<template>
    <div class="content-block content-block-med">
        <el-tabs tab-position="left">
            <el-tab-pane label="Profile">
                <el-col :span="22" :offset="1">
                    <el-form @submit.native.prevent="login" style="display: flex; flex-direction: column;">
                        <el-form-item label="Avatar">
                            <div style="display: flex;">
                                <el-upload
                                    id="avatar"
                                    :action="avatarUploadLink"
                                    :show-file-list="false"
                                    :with-credentials="true"
                                    :http-request="uploadAvatar"
                                    list-type="picture-card">
                                    <i class="el-icon-plus avatar-uploader-icon"></i>
                                </el-upload>
                                <label for="file">
                                    <avatar style="margin-left: 0.5rem;" large v-if="userAvatar" :src="userAvatar"/>
                                </label>
                            </div>
                        </el-form-item>
                        <el-form-item label="Name" prop="name">
                            <el-input v-model="name"/>
                        </el-form-item>
                        <el-form-item label="Email">
                            <el-input v-model="email"/>
                        </el-form-item>
                        <el-form-item label="Passowrd">
                            <el-input type="password" v-model="password"/>
                        </el-form-item>
                        <span v-if="error">{{error}}</span>
                        <el-input style="width: initial; margin-left: auto; margin-right: auto;" type="submit" value="Save"/>
                    </el-form>
                </el-col>
            </el-tab-pane>
            <el-tab-pane label="Options">

            </el-tab-pane>
        </el-tabs>
    </div>
</template>
<script>
import { mapGetters } from 'vuex'

export default {
    data() {
        return {
            name: '',
            email: '',
            password: '',
            remember: true,
            error: false
        }
    },
    computed: {
        avatarUploadLink() {
            return `http://localhost:8000/user/${this.$store.getters.userId}/avatar`;
        },
        ...mapGetters([
            'userAvatar'
        ])
    },
    middleware({ store, redirect, error }) { //TODO: error page
        if (!store.state.user) {
            error({
                statusCode: 401,
                message: 'You must be logged in to enter this page!'
            });
        }
    },
    methods: {
        async uploadAvatar(params) {
            console.log(params);
            const formData = new FormData();
            formData.append('file', params.file);
            const newAvatar = await this.$axios.post(`http://localhost:8000/users/${this.$store.getters.userId}/avatar`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(res => res.data);
            this.$store.commit('setUserAvatar', newAvatar);
        },
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
<style scoped>
    .avatar {
        width: 150px;
        height: 150px;
        border-radius: 5%;
    }
</style>