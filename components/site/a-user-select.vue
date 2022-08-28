<template>
    <div>
        <a-select v-model="modelValue" :options="users" text-by="text" @update-search="searchUsers" @update:model-value="value => $emit('update:modelValue', value)"/>
    </div>
</template>

<script setup lang="ts">
import { User } from '~~/types/models';

const props = defineProps<{
    modelValue: User|Array<User>,
}>();

defineEmits(['update:modelValue']);

let lastTimetout: NodeJS.Timeout;
const query = ref('');

interface SearchUser extends User {
    text: string
}

const { data: paginatedUsers, refresh } = await useAsyncData(() => {
    let user_id;

    if (typeof(props.modelValue) == 'number') {
        user_id = props.modelValue;
    }

    console.log('..');
    

    return useGetMany<SearchUser>('users', { params: { query: query.value, user_id } });
});

function searchUsers(q: string) {
    query.value = q;

    console.log('...');
    

    if (lastTimetout) {
        clearTimeout(lastTimetout);
    }
    lastTimetout = setTimeout(refresh, 200);
}

const users = computed(() => {
    return paginatedUsers.value.data.filter(user => {
        user.text = user.name + ` (${user.unique_name}#${user.id})`;
        return user;
    });
});
</script>