<template>
    <m-flex column gap="4" style="flex: 1;">
        <m-flex v-if="title || $slots.title" class="items-center">
            <slot name="title">
                <h2 v-if="title">{{title}}</h2>
            </slot>
            <slot name="buttons" :items="items"/>
        </m-flex>


        <m-flex v-if="search || typeof newButton == 'string'" column gap="3">
            <m-flex>
                <m-input v-if="search" v-model="queryRef" :label="$t('search')"/>
                <m-button v-if="typeof newButton == 'string'" class="mt-auto" :to="newButton">{{$t('new')}}</m-button>
            </m-flex>
        </m-flex>

        <m-pagination v-if="pagination" v-model="page" :total="total" :per-page="limit">
            <slot name="pagination" :items="items"/>
        </m-pagination>

        <m-flex column :gap="gap">
            <slot v-if="items.data.length" name="items" :items="items">
                <slot v-for="item of items.data" :key="item.id" name="item" :item="item" :items="items">
                    <m-list-item :item="item" :text-by="textBy" :to="itemLink" class="gap-3">
                        <slot name="item-name" :item="item"/>
                        <template #before-item>
                            <slot name="before-item" :item="item" :items="items"/>
                        </template>
                        <template #item-buttons>
                            <slot name="item-buttons" :item="item" :items="items"/>
                        </template>
                    </m-list-item>
                </slot>
            </slot>
            <m-loading v-else-if="isLoading"/>
            <h3 v-else class="mx-auto p-4">
                {{$t('nothing_found')}}
            </h3>
        </m-flex>

        <m-pagination v-if="pagination && !isLoading" v-model="page" :total="total" :per-page="limit">
            <slot name="pagination" :items="items"/>
        </m-pagination>
    </m-flex>
</template>

<script setup lang="ts">
import { Paginator } from '~/types/paginator';

const props = withDefaults(defineProps<{
    title?: string,
    newButton?: string|boolean,
    url?: string,
    search?: boolean,
    column?: boolean
    textBy?: string,
    gap?: number,
    limit?: number|string,
    query?: boolean,
    params?: object,
    itemLink?: (item?) => string,
    pagination?: boolean,
    loading?: boolean,
    items?: Paginator<any>,
}>(), {
    search: false,
    gap: 1,
    limit: 50,
    query: false,
    pagination: true,
    column: true
});

const vmPage = defineModel<number>('page');
const page = props.query ? useRouteQuery('page', 1) : ref(vmPage.value);

const queryRef = props.query ? useRouteQuery('query', '') : ref('');

const { data: loadedItems, loading: innerLoading, error } = await useWatchedFetchMany(props.url ?? '', Object.assign(props.params || {}, {
    page: page,
    query: queryRef,
    limit: props.limit
}), { immediate: !!props.url});

useHandleError(error);

const items = computed<Paginator<any>>(() => props.items ?? loadedItems.value ?? new Paginator());
const total = computed(() => items.value.meta?.total ?? 0);
const isLoading = computed(() => innerLoading.value || props.loading);

watch(page, (value) => {
    vmPage.value = value;
}, { immediate: true });
</script>