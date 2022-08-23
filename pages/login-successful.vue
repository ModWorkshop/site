<template>
    <page-block class="items-center">
        Login Succesful. Please wait a moment.
    </page-block>
</template>
<script setup lang="ts">
//TEMPORARY SOLUTION!
import { User } from '~~/types/models';
import { useStore } from '../store';

const store = useStore();
const route = useRoute();
const router = useRouter();

await useGet(`/auth/steam/callback`, { params: route.query }).catch(err => console.log(err));
const { data: userData } = await useFetchData<User>('/user');
store.user = userData.value;
router.push('/');
</script>