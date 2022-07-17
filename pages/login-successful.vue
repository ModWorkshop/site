<template>
    <page-block>
        Login Succesful. Please wait a moment.
    </page-block>
</template>
<script setup>
//TEMPORARY SOLUTION!
import { useStore } from '../store';

const store = useStore();
const router = useRouter();

await useFetchData(`/auth/steam/callback${window.location.search}`);
const { data: userData } = await useFetchData('/user');
store.user = userData;
router.push('/');
</script>