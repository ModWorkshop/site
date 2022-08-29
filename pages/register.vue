<template>
    <page-block class="!w-1/2">
        <a-form autocomplete="off" @submit="register">
            <content-block column gap="3" class="p-4">
                <img-uploader v-model="avatarBlob" label="Avatar">
                    <template #label="{ src }">
                        <a-avatar size="xl" :src="src"/>
                        <a-avatar size="lg" :src="src"/>
                        <a-avatar size="md" :src="src"/>
                    </template>
                </img-uploader>
                <a-input v-model="user.name" autocomplete="off" label="Display Name"/>
                <a-input v-model="user.unique_name" autocomplete="off" label="Unique Name"/>
                <a-input v-model="user.email" autocomplete="off" label="Email"/>
                <flex>
                    <a-input v-model="user.password" autocomplete="off" label="Password" type="password"/>
                    <a-input v-model="user.password_confirm" autocomplete="off" label="Confirm Password" type="password" @input="checkConfirm"/>
                </flex>
                <flex column gap="2">
                    Or register using one the following
                    <flex>
                        <a-button :href="`${config.apiUrl}/auth/steam/redirect`" :icon="['fab', 'steam']" icon-size="lg"/>
                        <a-button :icon="['fab', 'google']" icon-size="lg"/>
                        <a-button :icon="['fab', 'twitter']" icon-size="lg"/>
                    </flex>
                </flex>
                <a-alert v-if="error" color="danger" class="w-full">{{error}}</a-alert>
                <div>
                    <a-button type="submit" :disabled="!canRegister">{{$t('register')}}</a-button>
                </div>
            </content-block>
        </a-form>    
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '../store';

definePageMeta({
    middleware: 'guests-only'
});

const user = reactive({
    name: '',
    unique_name: '',
    email: '',
    password: '',
    password_confirm: '',
});

const error = ref('');
const avatarBlob = ref(null);

const canRegister = computed(() => user.name && user.email && user.unique_name && user.password && user.password_confirm);

const store = useStore();
const { public: config } = useRuntimeConfig();

async function register() {
    if (user.password_confirm !== user.password) {
        return;
    }
    error.value = '';

    try {
        await usePost('/register', serializeObject({...user, avatar_file: avatarBlob.value}));  
        store.attemptLoginUser();
    } catch (e) {
        if (e.response.status == 409) {
            error.value = 'The given unique name or email already exist!';
        } else {
            error.value = 'Something went wrong';
        }
        console.log(e);
    }
}

function checkConfirm() {
    if (user.password_confirm !== user.password) {
        error.value = "Passwords must match!";
    } else {
        error.value = '';
    }
}
</script>