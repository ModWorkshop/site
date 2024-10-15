<template>
    <m-flex column gap="2" style="flex: 1;">
        <slot name="title">
            <h2 v-if="title">{{title}}</h2>
        </slot>

        <slot name="buttons" :items="compItems"/>

        <m-flex column gap="3">
            <m-flex>
                <m-input v-if="search" v-model="queryRef" :label="$t('search')"/>
                <m-button v-if="typeof newButton == 'string'" class="mt-auto" :to="newButton">{{$t('new')}}</m-button>
            </m-flex>
        </m-flex>

        <m-pagination v-if="pagination" v-model="page" :total="total" :per-page="limit">
            <slot name="pagination" :items="compItems"/>
        </m-pagination>

        <m-flex column :gap="gap">
            <m-loading v-if="isLoading"/>
            <slot v-else-if="compItems && compItems?.data.length" name="items" :items="compItems">
                <slot v-for="item of compItems.data" :key="item.id" name="item" :item="item" :items="compItems">
                    <m-list-item :item="item" :text-by="textBy" :to="itemLink">
                        <slot name="item-name" :item="item"/>
                        <template #before-item>
                            <slot name="before-item" :item="item"/>
                        </template>
                        <template #item-buttons>
                            <slot name="item-buttons" :item="item"/>
                        </template>
                    </m-list-item>
                </slot>
            </slot>
            <span v-else class="p-4">
                {{$t('nothing_found')}}
            </span>
        </m-flex>

        <m-pagination v-if="pagination && !isLoading" v-model="page" :total="total" :per-page="limit">
            <slot name="pagination" :items="items"/>
        </m-pagination>
    </m-flex>
</template>

<script setup lang="ts">
import { Paginator } from '~~/types/paginator';

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
    items?: Paginator<any>|null,
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

const { data: items, loading: innerLoading, error } = await useWatchedFetchMany(props.url ?? '', Object.assign(props.params || {}, {
    page: page,
    query: queryRef,
    limit: props.limit
}), { immediate: !!props.url});

useHandleError(error);

const compItems = computed<Paginator<any>|null>(() => props.items ?? items.value);
const total = computed(() => compItems.value?.meta?.total ?? 0);
const isLoading = computed(() => innerLoading.value || props.loading);

watch(page, (value) => {
    vmPage.value = value;
}, { immediate: true });
</script>