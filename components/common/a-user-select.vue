<template>
    <a-select v-model="modelValue" :options="users" text-by="text" @update-search="searchUsers" @update:model-value="value => $emit('update:modelValue', value)"/>
</template>

<script setup lang="ts">
import { User } from '~~/types/models';

defineProps<{
    modelValue: User|Array<User>,
}>();

defineEmits(['update:modelValue']);

let lastTimetout: NodeJS.Timeout;
const users = ref([]);

interface SearchUser extends User {
    text: string
}

function searchUsers(query) {
    if (lastTimetout) {
        clearTimeout(lastTimetout);
    }
    lastTimetout = setTimeout(async () => {
        const usersPaginated = await useGetMany<SearchUser>('users', { params: { query } });
        users.value = usersPaginated.data.filter(user => {
            user.text = user.name + ` (${user.unique_name}#${user.id})`;
            return user;
        });
    }, 200);
}

</script>