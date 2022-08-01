<template>
    <span :title="fullDateStr">{{timeAgoStr}}</span>
</template>

<script setup>
const props = defineProps({
    time: String
});

const timeAgoStr = computed(() => timeAgo(props.time));
const fullDateStr = computed(() => fullDate(props.time));
</script>