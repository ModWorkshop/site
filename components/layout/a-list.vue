<template>
    <flex column gap="2">
        <flex>
            <a-input v-if="search" v-model="query" placeholder="Search" @update:model-value="onSearch"/>
            <a-button v-if="typeof newButton == 'string'" class="my-auto" :to="newButton">New</a-button>
            <slot name="buttons" :items="items"/>
        </flex>

        <a-pagination v-model="page" :total="items.meta.total" :per-page="limit" @update="refresh">
            <slot name="pagination" :items="items"/>
        </a-pagination>

        <flex column>
            <a-loading v-if="loading"/>
            <slot v-else name="items" :items="items">
                <template v-if="items.data.length">
                    <template v-for="item of items.data" :key="item.id">
                        <slot name="item" :item="item" :items="items">
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
const props = defineProps({
    newButton: [String, Boolean],
    url: String,
    modelValue: Object,
    search: {
        type: Boolean,
        default: true
    },
    textBy: {
        type: String,
        default: 'name'
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

const emit = defineEmits(['update:modelValue']);

const page = props.query ? useRouteQuery('page', 1) : ref(1);
const route = useRoute();
const query = props.query ? ref(route.query.query) : ref('');
const loading = ref(true);

const params = reactive(Object.assign(props.params || {}, {
    query: query,
    page: page,
    limit: props.limit,
}));

const { data: items, refresh, error } = await useFetchMany(props.url, { params });

useHandleError(error);

watch(items, val => {
    emit('update:modelValue', val);
});

emit('update:modelValue', items.value);

watch(params, refresh);

loading.value = false;

function onSearch(value: string) {
    query.value = value;
    page.value = 1;
    refresh();
}
</script>