<template>
    <span :title="fullDate(time)">{{timeAgo(time)}}</span>
</template>

<script setup>
defineProps({
    time: String
});
</script>