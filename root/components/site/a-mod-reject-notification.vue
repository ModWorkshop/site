<template>
    <flex class="items-center ">
        <a-icon icon="mdi:alert-box" class="text-4xl p-2 my-auto"/>
        <div>
            <i18n-t keypath="notification_mod_rejected" tag="span" style="word-wrap: anywhere;" scope="global">
                <template #notifiable>
                    <a-notification-slot type="mod" :object="notifiable"/>
                </template>
            </i18n-t>
            <template v-if="data.reason">
                <h4>{{ $t('reason') }}:</h4>
                <blockquote>{{data.reason}}</blockquote>
            </template>
        </div>
    </flex>
</template>

<script setup lang="ts">
import { Notification, UserCase } from '~~/types/models';

defineProps<{
    notification: Notification,
    notifiable: UserCase,
    data: any
}>();
</script>