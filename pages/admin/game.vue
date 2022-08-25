<template>
    <page-block size="md">
        <content-block class="p-6">
            <a-nav side :root="`/admin/games/${id}`">
                <a-nav-link to="" title="Main"/>
                <a-nav-link to="tags" title="Tags"/>
                <a-nav-link to="categories" title="Categories"/>
                <a-nav-link to="forum-categories" title="Forum Categories"/>
                <a-nav-link to="mods" title="Mods"/>
                <template #content>
                    <NuxtPage/>
                </template>
            </a-nav>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
definePageMeta({
    middleware: 'admins-only'
});

const route = useRoute();
const id = computed(() => route.params.gameId);
</script>

<style scoped>

</style>