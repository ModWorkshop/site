<template>
    <div class="text-center">
        <h1 v-if="error.statusCode === 404">Page not found!</h1>
        <h1 v-else-if="error.message">{{error.message}}</h1>
        <h1 v-else>An error occurred</h1>
        <nuxt-link to="/">Home page</nuxt-link>
    </div>
</template>

<script setup>
    defineProps({
        error: Object        
    });
</script>