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
    import { useContext, useFetch } from "@nuxtjs/composition-api";

    let roles = $ref([]);
    const { $axios } = useContext();

    useFetch(async () => {
        roles = await $axios.get('/roles').then(res => res.data.data);
    });
</script>

<style scoped>

</style>