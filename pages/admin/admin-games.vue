<template>
    <a-list url="games" :item-link="item => `/admin/games/${item.id}`" new-button="/admin/games/new"/>
</template>

<script setup>
</script>