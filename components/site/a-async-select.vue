<template>
    <a-select v-model="modelValue" :options="paginator?.data" @update-search="searchUsers" @update:model-value="value => $emit('update:modelValue', value)"/>
</template>

<script setup lang="ts">
const props = defineProps({
    modelValue: [Object, Array, String, Number],
    url: String,
});

defineEmits(['update:modelValue']);

const query = ref('');

const { data: paginator, refresh } = await useFetchMany(props.url, { params: reactive({ query }), initialCache: true });

const { start } = useTimeoutFn(refresh, 200, { immediate: false });

function searchUsers(q: string) {
    query.value = q;
  start();
}
</script>