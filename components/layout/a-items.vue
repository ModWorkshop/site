<template>
    <flex column gap="2" style="flex: 1;">
        <a-pagination v-if="!loading" v-model="vPage" :total="total" :per-page="perPage">
            <slot name="pagination" :items="items"/>
        </a-pagination>

        <a-loading v-if="loading" class="m-auto"/>
        <flex column :gap="gap" padding="3">
            <slot v-if="!loading" name="items" :items="items">
                <template v-if="items?.data.length">
                    <slot v-for="item of items.data" :key="item.id" name="item" :item="item"/>
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
    loading?: boolean
}>();

const emit = defineEmits<{
    (e: 'update:page', page: number|null): void
}>();

const total = computed(() => props.items?.meta?.total ?? 0);
const perPage = computed(() => props.items?.meta?.per_page ?? 1);
const vPage = useVModel(props, 'page', emit);
</script>

<style>

.fade-x-enter,
.fade-x-leave-to {
  opacity: 0;
  transform: translateX(20px);
}

.fade-x-enter-active,
.fade-x-leave-to {
  transition: opacity 0.3s, transform 0.5s;
}
.roll-in-left-enter,
.roll-in-left-leave-to{
    transform: translateX(-500px) rotate(-200deg);
    opacity: 0;
}

.roll-in-left-enter-active,
.roll-in-left-leave-active{
    transition:  transform 0.3s, opacity 0.5s ;
}
.rotate-enter,
.rotate-leave-to{
    transform: rotate(-360deg);
    opacity: 0;
}

.rotate-enter-active,
.rotate-leave-active{
    transition:  transform 0.7s, opacity 0.5s ;
}
</style>