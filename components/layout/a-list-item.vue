<template>
    <NuxtLink class="list-button" :to="typeof to == 'function' ? to(item) : to">
        <slot>
            <slot name="before-item" :item="item"/>
            <slot :item="item">
                <span class="my-auto">{{item[textBy]}}</span>
            </slot>
            <flex class="ml-auto my-auto">
                <slot name="item-buttons" :item="item"/>
            </flex>
        </slot>
    </NuxtLink>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
    to?: string|((item?) => string),
    item?: any,
    textBy?: string,
}>(), { textBy: 'name' });
</script>