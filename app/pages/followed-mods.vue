<template>
    <page-block>
        <mod-list 
            :limit="20"
            :url="selectedView"
            side-filters
        >
            <template #buttons>
                <m-toggle-group v-model:selected="selectedView" button-style="nav" :wrap="false" class="overflow-auto">
                    <m-flex class="flex-shrink-0">
                        <m-toggle-group-item value="mods/followed"><i-mdi-plus-thick/> {{$t('all')}}</m-toggle-group-item>
                        <m-toggle-group-item value="followed-games/mods"><i-mdi-gamepad/> {{$t('followed_games')}}</m-toggle-group-item>
                        <m-toggle-group-item value="followed-mods"><i-mdi-puzzle/> {{$t('followed_mods')}}</m-toggle-group-item>
                        <m-toggle-group-item value="followed-users/mods"><i-mdi-users/> {{$t('followed_users')}}</m-toggle-group-item>
                    </m-flex>
                </m-toggle-group>
            </template>
        </mod-list>
    </page-block>
</template>

<script setup lang="ts">
definePageMeta({
    middleware: 'users-only',
});

const selectedView = ref('mods/followed');
</script>