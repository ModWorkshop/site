<template>
    <page-block size="med">
        <content-block class="p-6">
            <a-nav :route="route" side>
                <a-nav-link to="/admin/home" title="Home"/>
                <a-nav-link to="/admin/roles" title="Roles"/>
                <template #content>
                    <slot/>
                </template>
            </a-nav>
        </content-block>
    </page-block>
</template>

<script setup>
    defineProps({
        route: String,
    });
</script>

<style scoped>

</style>