<template>
    <flex column gap="2">
        <flex column gap="3">
            <flex>
                <a-input v-if="search" v-model="query" :label="$t('search')"/>
                <a-button v-if="typeof newButton == 'string'" class="mt-auto" :to="newButton">{{$t('new')}}</a-button>
            </flex>
            <slot name="buttons" :items="items"/>
        </flex>

        <a-pagination v-if="pagination" v-model="page" :total="items?.meta?.total" :per-page="limit">
            <slot name="pagination" :items="items"/>
        </a-pagination>

        <flex column :gap="gap" padding="3">
            <a-loading v-if="loading"/>
            <slot v-else-if="items && items?.data.length" name="items" :items="items">
                <slot v-for="item of items.data" :key="item.id" name="item" :item="item" :items="items">
                    <a-list-item :item="item" :text-by="textBy" :to="itemLink">
                        <slot name="item-name" :item="item"/>
                        <template #before-item>
                            <slot name="before-item" :item="item"/>
                        </template>
                        <template #item-buttons>
                            <slot name="item-buttons" :item="item"/>
                        </template>
                    </a-list-item>
                </slot>
            </slot>
            <span v-else class="p-4">
                {{$t('nothing_found')}}
            </span>
        </flex>

        <a-pagination v-if="pagination" v-model="page" :total="items?.meta.total" :per-page="limit">
            <slot name="pagination" :items="items"/>
        </a-pagination>
    </flex>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    newButton?: string|boolean,
    url: string,
    search?: boolean,
    textBy?: string,
    gap?: number,
    limit?: number|string,
    query?: boolean,
    params?: object,
    itemLink?: (item?) => string,
    pagination: boolean
}>(), {
    search: true,
    gap: 1,
    limit: 50,
    query: false,
    pagination: true
});

const page = props.query ? useRouteQuery('page', 1) : ref(1);
const query = props.query ? useRouteQuery('query', '') : ref('');

const { data: items, loading, error } = await useWatchedFetchMany(props.url, Object.assign(props.params || {}, {
    page: page,
    query: query,
    limit: props.limit,
}));

useHandleError(error);
</script>