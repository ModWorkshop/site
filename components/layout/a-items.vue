<template>
    <flex column gap="2" style="flex: 1;">
        <a-pagination v-if="!loading" v-model="vPage" :total="total" :per-page="perPage">
            <slot name="pagination" :items="items"/>
        </a-pagination>

        <a-loading v-if="loading" class="m-auto"/>
        <flex column :gap="gap" class="px-4 py-1">
            <slot v-if="!loading" name="items" :items="items">
                <template v-if="items?.data.length">
                    <slot v-for="item of items.data" :key="item.id" name="item" :item="item">
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
                </template>
                <span v-else class="p-4">
                    {{$t('nothing_found')}}
                </span>
            </slot>
        </flex>

        <a-pagination v-if="!loading" v-model="vPage" :total="total" :per-page="perPage">
            <slot name="pagination"/>
        </a-pagination>
    </flex>
</template>

<script setup lang="ts">
import { Paginator } from '~~/types/paginator';

const props = defineProps<{
    gap?: number,
    page?: number,
    items: Paginator<any>|null,
    loading?: boolean,
    itemLink?: (item?) => string,
    textBy?: string,
}>();

const emit = defineEmits<{
    (e: 'update:page', page: number|null): void
}>();

const total = computed(() => props.items?.meta?.total ?? 0);
const perPage = computed(() => props.items?.meta?.per_page ?? 1);
const vPage = useVModel(props, 'page', emit);
</script>