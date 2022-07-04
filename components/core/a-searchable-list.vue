<template>
    <flex column gap="2">
        <flex>
            <a-input placeholder="Search" v-model="query" @update:modelValue="onSearch"/>
            <a-button class="my-auto" to="/admin/tags/new">New</a-button>
        </flex>

        <a-pagination v-model="page" :total="items.meta.total" perPage="50" @update="refresh"/>

        <flex column>
            <nuxt-link class="list-button flex-grow" v-for="item of items.data" :key="item.id" :to="itemLink(item)">
                {{item.name}}
                <slot name="item" :item="item"/>
            </nuxt-link>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { setQuery } from '../../utils/helpers';

const props = defineProps({
    newButton: [String, Boolean],
    url: String,
    itemLink: Function
});

const page = ref(1);
const route = useRoute();
const query = ref(route.query.query);

const { data: items, refresh } = await useAsyncDyn('get-items-'+props.url, () => useGet(props.url, { 
    params: {
        query: query.value,
        page: page.value
    }
}));

function onSearch(value: string) {
    setQuery('query', value);
    page.value = 1;
    refresh();
}
</script>