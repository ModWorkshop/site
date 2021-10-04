<template>
    <admin-page route="roles">
        <div class="mb-3">
            <a-button to="/admin/roles/new">New</a-button>
        </div>
        <flex column gap="1" grow>
            <nuxt-link class="role-button flex-grow" v-for="role of roles" :key="role.id" :to="`/admin/roles/${role.id}`">
                {{role.name}}
                <br>
                <small v-if="role.id == 1">
                    All members have this role
                </small>
            </nuxt-link>
        </flex>
    </admin-page>
</template>

<script setup>
    import { useContext, useAsync } from '@nuxtjs/composition-api';

    const { $axios } = useContext();

    const roles = useAsync(async () => {
        const res = await $axios.get('/roles').then(res => res.data);

        return res.data;        
    });
</script>

<style scoped>
    .role-button {
        color: var(--text-color);
        background-color: var(--alt-bg-color);
        padding: 1rem;
    }
</style>