<template>
    <flex column gap="2">
        <flex column gap="3">
            <flex>
                <a-input v-if="search" v-model="query" :label="$t('search')"/>
                <a-button v-if="typeof newButton == 'string'" class="mt-auto" :to="newButton">{{$t('new')}}</a-button>
            </flex>
            <slot name="buttons" :items="items"/>
        </flex>

        <a-pagination v-model="page" :total="items?.meta?.total" :per-page="limit" @update="refresh">
            <slot name="pagination" :items="items"/>
        </a-pagination>

        <flex column :gap="gap" padding="3">
            <a-loading v-if="loading"/>
            <slot v-else name="items" :items="items">
                <template v-if="items?.data.length">
                    <slot v-for="item of items.data" :key="item.id" name="item" :item="item" :items="items">
                        <NuxtLink class="list-button flex gap-2" :to="itemLink ? itemLink(item) : undefined">
                            <slot name="before-item" :item="item" :items="items"/>
                            <slot :item="item">
                                <span class="my-auto">{{item[textBy]}}</span>
                            </slot>
                            <flex class="ml-auto my-auto">
                                <slot name="item-buttons" :item="item" :items="items"/>
                            </flex>
                        </NuxtLink>
                    </slot>
                </template>
                <span v-else class="p-4">
                    {{$t('nothing_found')}}
                </span>
            </slot>
        </flex>

        <a-pagination v-model="page" :total="items?.meta.total" :per-page="limit" @update="refresh">
            <slot name="pagination"/>
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
    itemLink?: (item) => string,
}>(), {
    search: true,
    textBy: 'name',
    gap: 1,
    limit: 50,
    query: false
});

const page = props.query ? useRouteQuery('page', 1) : ref(1);
const query = props.query ? useRouteQuery('query', '') : ref('');
const loading = ref(true);

const params = reactive(Object.assign(props.params || {}, {
    page: page,
    query: query,
    limit: props.limit,
}));

const { data: items, refresh, error } = await useFetchMany(props.url, { params });

useHandleError(error);

let { start: planLoad } = useTimeoutFn(async () => {
    await refresh();
    loading.value = false;
}, 250, { immediate: false });

watch([
    page,
    query,
    () => props.limit
], async (val, oldVal) => {
    if (val[0] !== oldVal[0]) {
        page.value = 1;
    }
    loading.value = true;
    planLoad();
});

loading.value = false;
</script>