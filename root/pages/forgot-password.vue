<template>
    <page-block size="2xs">
        <Title>{{$t('forgot_password')}}</Title>
        <m-form @submit="reset">
            <h1>{{$t('forgot_password')}}</h1>
            <m-content-block column gap="3">
                <m-input v-model="email" :disabled="sent" :label="$t('email')" type="email"/>
                <div>
                    <m-button type="submit" :disabled="!email || sending">{{$t('send_link')}}</m-button>
                </div>
                <div v-if="sent">{{$t('password_reset_sent')}}</div>
            </m-content-block>
        </m-form>    
    </page-block>
</template>

<script setup lang="ts">
definePageMeta({
    middleware: 'guests-only'
});

const email = ref('');
const sent = ref(false);
const sending = ref(false);

const showError = useQuickErrorToast();

async function reset() {
    sending.value = true;
    try {
        await postRequest('forgot-password', {
            email: email.value
        });
        sent.value = true;
    } catch (error) {
        showError(error);
        sending.value = false;
    }
}
</script>