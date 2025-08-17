<template>
    <page-block>
        <mod-list side-filters query url="mods/liked">
            <template #sort-buttons="{ sortBy, setSortBy }">
                <m-button :disabled="sortBy == 'liked_at'" @click="setSortBy('liked_at')">
                    <i-mdi-heart/> {{$t('liked_at')}}
                </m-button>
            </template>
        </mod-list>
    </page-block>
</template>

<script setup>
definePageMeta({
    middleware: 'users-only',
});
</script>