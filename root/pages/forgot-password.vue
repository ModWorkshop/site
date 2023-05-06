<template>
    <page-block size="2xs">
        <Title>{{$t('forgot_password')}}</Title>
        <a-form @submit="reset">
            <h1>{{$t('forgot_password')}}</h1>
            <content-block column gap="3">
                <a-input v-model="email" :disabled="sent" :label="$t('email')" type="email"/>
                <div>
                    <a-button type="submit" :disabled="!email || sending">{{$t('send_link')}}</a-button>
                </div>
                <div v-if="sent">{{$t('password_reset_sent')}}</div>
            </content-block>
        </a-form>    
    </page-block>
</template>

<script setup lang="ts">
definePageMeta({
    middleware: 'guests-only'
});

const email = ref('');
const sent = ref(false);
const sending = ref(false);

async function reset() {
    sending.value = true;
    await usePost('forgot-password', {
        email: email.value
    });
    sent.value = true;
}
</script>