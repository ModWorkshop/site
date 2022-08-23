<template>
    <flex column gap="2">
        <flex>
            <a-input v-if="search" v-model="query" placeholder="Search" @update:model-value="onSearch"/>
            <a-button v-if="newButton" class="my-auto" :to="newButton">New</a-button>
            <slot name="buttons"/>
        </flex>

        <a-pagination v-model="page" :total="items.meta.total" :per-page="limit" @update="refresh">
            <slot name="pagination"/>
        </a-pagination>

        <flex column>
            <template v-for="item of items.data" :key="item.id">
                <slot name="item" :item="item" :items="items">
                    <NuxtLink class="list-button flexbox gap-1" :to="itemLink(item)">
                        <slot name="before-item" :item="item"/>
                        <slot :item="item">
                            <span class="my-auto">{{item.name}}<slot name="item-name" :item="item"/></span>
                        </slot>
                        <slot name="after-item" :item="item"/>
                    </NuxtLink>
                </slot>
            </template>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { setQuery } from '../../utils/helpers';

const props = defineProps({
    newButton: [String, Boolean],
    url: String,
    search: {
        type: Boolean,
        default: true
    },
    limit: {
        type: [Number, String],
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