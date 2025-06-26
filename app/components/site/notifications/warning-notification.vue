<template>
    <m-flex>
        <i-mdi-alert-box class="text-4xl p-2 my-auto"/>
        <div>
            {{ $t('notification_warning') }}
            <h4>{{ $t('reason') }}:</h4>
            <blockquote>{{notifiable.reason}}</blockquote>
        </div>
    </m-flex>
</template>

<script setup lang="ts">
import type { Notification, UserCase } from '~/types/models';

defineProps<{
    notification: Notification,
    notifiable: UserCase
}>();
</script>