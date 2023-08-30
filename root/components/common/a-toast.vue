<template>
    <a-alert v-if="show" :title="title" :desc="desc" :color="color">
        <template #attach>
            <i-mdi-close v-if="closable" class="cursor-pointer ml-auto" @click="show = false"/>
        </template>
        <slot/>
    </a-alert>
</template>

<script setup lang="ts">
defineProps({
    title: String,
    desc: String,
    color: String,
    closable: {
        default: true,
        type: Boolean
    }
});

const show = ref(true);
</script>