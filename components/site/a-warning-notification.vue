<template>
    <flex>
        <a-icon icon="mdi:alert-box" class="text-4xl p-2 my-auto"/>
        <div>
            {{ $t('notification_warning') }}
            <h4>{{ $t('reason') }}:</h4>
            <blockquote>{{notifiable.reason}}</blockquote>
        </div>
    </flex>
</template>

<script setup lang="ts">
import { Notification, UserCase } from '~~/types/models';

defineProps<{
    notification: Notification,
    notifiable: UserCase
}>();
</script>