<template>
    <page-block>
        Login Succesful. Please wait a moment.
    </page-block>
</template>
<script setup>
//TEMPORARY SOLUTION!
import { useStore } from '../store';
import { useContext, onMounted, useRouter } from '@nuxtjs/composition-api';

const { $axios } = useContext();
const store = useStore();
const router = useRouter();

onMounted(async () => {
    await $axios.get(`/auth/steam/callback${window.location.search}`);
    const { data: userData } = await $axios.get('/user');
    store.user = userData;
    router.push('/');
});
</script>