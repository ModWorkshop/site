<template>
    <a-alert v-if="show" :title="title" :desc="desc" :color="color">
        <template #attach>
            <font-awesome-icon class="cursor-pointer ml-auto" icon="xmark" @click="show = false"/>
        </template>
        <slot/>
    </a-alert>
</template>

<script setup lang="ts">
defineProps({
    title: String,
    desc: String,
    color: String
});

const show = ref(true);
</script>

<style scoped>

</style>