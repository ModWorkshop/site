<template>
    <a-select 
        v-model="modelValue"
        :options="users"
        text-by="text"
        :placeholder="placeholder"
        @update-search="searchUsers"
        @update:model-value="value => $emit('update:modelValue', value)"
    />
</template>

<script setup lang="ts">
import { User } from '~~/types/models';

const props = withDefaults(defineProps<{
    modelValue: number|User|Array<User>,
    placeholder: string
}>(), { placeholder: 'Select a user...' });

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

    return useGetMany<SearchUser>('users', { params: { query: query.value, user_id } });
});

function searchUsers(q: string) {
    query.value = q;

    if (lastTimetout) {
        clearTimeout(lastTimetout);
    }
    lastTimetout = setTimeout(refresh, 200);
}

const users = computed(() => {
    return paginatedUsers.value.data.map(user => ({
        text: user.name + ` (${user.unique_name}#${user.id})`,
        ...user
    }));
});
</script>