<template>
    <div>
        Roles
        <flex>
            <div v-for="role of roles" :key="role.id">
                {{role.name}}
            </div>
        </flex>
    </div>
</template>

<script setup>
    import { useFetch } from "@nuxtjs/composition-api";

    const roles = ref([]);
    const { $axios } = useNuxtApp().legacyApp;

    useFetch(async () => {
        const {data: roles} = await $axios.get('/roles');
        roles.value = roles;
    });
</script>

<style scoped>

</style>