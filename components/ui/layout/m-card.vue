<template>
    <m-content-block class="card" :padding="0" :gap="0">
        <div v-if="$slots.thumbnail" class="card-thumbnail">
            <slot name="thumbnail"/>
        </div>
        <m-flex :class="{'items-center': center, 'card-content': true}" :padding="padding" column :gap="gap">
            <slot name="title">
                <strong v-if="title" class="card-title">
                    {{title}}
                </strong>
            </slot>
            <slot/>
        </m-flex>
    </m-content-block>
</template>

<script setup lang="ts">
const { padding = 4, gap = 2 } = defineProps<{
    title?: string,
    center?: boolean,
    padding?: number,
    gap?: number
}>();
</script>

<style>
.card {
    font-size: 13px;
}
.card-title {
    font-size: 1.15rem;
    font-weight: 600;
    line-height: 1.5rem;
    overflow: hidden;
    word-break: break-word;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.card-thumbnail, .card-thumbnail > * {
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
}
</style>

<style scoped>
.card-content {
    height: 100%;
}
</style>