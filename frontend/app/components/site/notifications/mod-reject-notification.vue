<template>
    <m-flex class="items-center ">
        <i-mdi-alert-box class="text-4xl p-2 my-auto"/>
        <div>
            <i18n-t keypath="notification_mod_rejected" tag="span" style="word-wrap: anywhere;" scope="global">
                <template #notifiable>
                    <base-notification type="mod" :object="notifiable"/>
                </template>
            </i18n-t>
            <template v-if="data.reason">
                <h4>{{ $t('reason') }}:</h4>
                <blockquote>{{data.reason}}</blockquote>
            </template>
        </div>
    </m-flex>
</template>

<script setup lang="ts">
import type { Notification, UserCase } from '~/types/models';

defineProps<{
    notification: Notification,
    notifiable: UserCase,
    data: any
}>();
</script>