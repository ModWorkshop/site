<template>
    <flex column gap="2">
        <flex>
            <a-input v-if="search" v-model="query" placeholder="Search"/>
            <a-button v-if="typeof newButton == 'string'" class="my-auto" :to="newButton">New</a-button>
            <slot name="buttons" :items="items"/>
        </flex>

        <a-pagination v-model="page" :total="items.meta?.total" :per-page="limit" @update="refresh">
            <slot name="pagination" :items="items"/>
        </a-pagination>

        <flex column :gap="gap">
            <a-loading v-if="loading"/>
            <slot v-else name="items" :items="items">
                <template v-if="items.data.length">
                    <slot v-for="item of items.data" :key="item.id" name="item" :item="item" :items="items">
                        <NuxtLink class="list-button flex gap-2" :to="itemLink ? itemLink(item) : null">
                            <slot name="before-item" :item="item" :items="items"/>
                            <slot :item="item">
                                <span class="my-auto">{{item[textBy]}}<slot name="item-name" :item="item"/></span>
                            </slot>
                            <slot name="after-item" :item="item" :items="items"/>
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

        <a-pagination v-model="page" :total="items.meta.total" :per-page="limit" @update="refresh">
            <slot name="pagination"/>
        </a-pagination>
    </flex>
</template>

<script setup lang="ts">
import { Paginator } from '~~/types/paginator';

const props = defineProps({
    newButton: [String, Boolean],
    url: String,
    search: {
        type: Boolean,
        default: true
    },
    textBy: {
        type: String,
        default: 'name'
    },
    gap: {
        type: Number,
        default: 1
    },
    limit: {
        type: [Number, String],
        default: 50
    },
    query: {
        type: Boolean,
        default: false
    },
    params: Object,
    itemLink: Function
});

const emit = defineEmits<{
    (e: 'fetched', items: Paginator)
}>();

const page = props.query ? useRouteQuery('page', 1) : ref(1);
const query = props.query ? useRouteQuery('query') : ref('');
const loading = ref(true);

const params = reactive(Object.assign(props.params || {}, {
    page: page,
    query: query,
    limit: props.limit,
}));

const { data: items, refresh, error } = await useFetchMany(props.url, { params });

useHandleError(error);

emit('fetched', items.value);

let { start: planLoad } = useTimeoutFn(async () => {
    await refresh();
    loading.value = false;
}, 250, { immediate: false });

watch(params, async (val, oldVal) => {
    if (val.page !== oldVal.page) {
        page.value = 1;
    }
    loading.value = true;
    planLoad();
});

loading.value = false;
</script>