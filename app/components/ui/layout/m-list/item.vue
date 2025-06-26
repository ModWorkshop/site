<template>
    <NuxtLink class="list-button" :to="typeof to == 'function' ? to(item) : to">
        <slot name="before-item" :item="item"/>
        <slot :item="item">
            <span class="my-auto">{{item[textBy]}}</span>
        </slot>
        <m-flex class="ml-auto my-auto">
            <slot name="item-buttons" :item="item"/>
        </m-flex>
    </NuxtLink>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
    to?: string|((item?) => string),
    item?: any,
    textBy?: string,
}>(), { textBy: 'name' });
</script>