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
            <slot name="items" :items="items">
                <template v-if="items.data.length">
                    <template v-for="item of items.data" :key="item.id">
                        <slot name="item" :item="item" :items="items">
                            <NuxtLink class="list-button flexbox gap-2" :to="itemLink(item)">
                                <slot name="before-item" :item="item"/>
                                <slot :item="item">
                                    <span class="my-auto">{{item.name}}<slot name="item-name" :item="item"/></span>
                                </slot>
                                <slot name="after-item" :item="item"/>
                            </NuxtLink>
                        </slot>
                    </template>
                </template>
                <span v-else class="p-4">
                    {{$t('nothing_found')}}
                </span>
            </slot>
        </flex>

        <a-pagination v-model="page" :total="items.meta.total" :per-page="limit" @update="refresh">
            <slot name="pagination"/>
        </a-pagination>
    </flex>
</template>

<script setup lang="ts">
import { setQuery } from '~~/utils/helpers';

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
    params: Object,
    itemLink: Function
});

const page = useRouteQuery('page', 1);
const route = useRoute();
const query = ref(route.query.query);

const { data: items, refresh } = await useFetchMany(props.url, { 
    params: reactive(Object.assign({
        query: query,
        page: page,
        limit: props.limit,
    }, props.params))
});

function onSearch(value: string) {
    setQuery('query', value);
    page.value = 1;
    refresh();
}
</script>