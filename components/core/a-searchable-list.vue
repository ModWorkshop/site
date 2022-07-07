<template>
    <flex column gap="2">
        <flex>
            <a-input placeholder="Search" v-model="query" @update:modelValue="onSearch"/>
            <a-button class="my-auto" to="/admin/tags/new">New</a-button>
        </flex>

        <a-pagination v-model="page" :total="items.meta.total" :perPage="limit" @update="refresh"/>

        <flex column>
            <nuxt-link class="list-button flexbox gap-1" v-for="item of items.data" :key="item.id" :to="itemLink(item)">
                <slot name="before-item" :item="item"/>
                <span class="my-auto">{{item.name}}<slot name="item" :item="item"/></span>
                <slot name="after-item" :item="item"/>
            </nuxt-link>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { setQuery } from '../../utils/helpers';

const props = defineProps({
    newButton: [String, Boolean],
    url: String,
    limit: {
        type: [String, Boolean],
        default: 50
    },
    itemLink: Function
});

const page = ref(1);
const route = useRoute();
const query = ref(route.query.query);

const { data: items, refresh } = await useAsyncDyn('get-items-'+props.url, () => useGet(props.url, { 
    params: {
        query: query.value,
        page: page.value,
        limit: props.limit
    }
}));

function onSearch(value: string) {
    setQuery('query', value);
    page.value = 1;
    refresh();
}
</script>