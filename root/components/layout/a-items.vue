<template>
    <flex column gap="2" style="flex: 1;">
        <slot name="title">
            <h2 v-if="title">{{title}}</h2>
        </slot>
        
        <slot name="buttons"/>

        <a-pagination v-model="page" :total="total" :per-page="perPage">
            <slot name="pagination" :items="items"/>
        </a-pagination>

        <a-loading v-if="loading" class="m-auto"/>
        <flex column :gap="gap" class="py-1">
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

        <a-pagination v-if="!loading" v-model="page" :total="total" :per-page="perPage">
            <slot name="pagination"/>
        </a-pagination>
    </flex>
</template>

<script setup lang="ts">
import { Paginator } from '~~/types/paginator';

const props = defineProps<{
    title?: string,
    gap?: number,
    items: Paginator<any>|null,
    loading?: boolean,
    itemLink?: (item?) => string,
    textBy?: string,
}>();

const total = computed(() => props.items?.meta?.total ?? 0);
const perPage = computed(() => props.items?.meta?.per_page ?? 1);
const page = defineModel<number>('page', { default: 1, local: true });
</script>