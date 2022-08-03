<template>
    <page-block size="md">
        <content-block>
            <a-list url="notifications" limit="20" :search="false" class="p-4">
                <template #pagination>
                    <flex class="ml-auto">
                        <a-button color="danger" icon="trash">{{$t('delete_all_notifications')}}</a-button>
                        <a-button color="danger" icon="clock">{{$t('delete_read_notifications')}}</a-button>
                    </flex>
                </template>
                <template #item="{ item }">
                    <a-notification :notification="item"/>
                </template>
            </a-list>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">

</script>

<style scoped>

</style>