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
    const { data: roles } = useAPIFetch('/roles');
</script>