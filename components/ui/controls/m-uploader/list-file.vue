<template>
    <m-content-block alt-background :column="false" wrap>
        <slot name="before-info" :file="file"/>
        <m-flex column class="break-words overflow-hidden">
            <span>{{file.name}} ({{friendlySize(file.size)}})</span>
            <span v-if="paused">{{pausedReason ?? $t('waiting')}}</span>
            <span v-if="file.progress">{{$t('uploading', [file.progress])}}</span>
            <span v-else-if="file.created_at">{{fullDate(file.created_at)}}</span>
        </m-flex>
        <slot name="after-info" :file="file"/>
        <m-flex class="ml-auto">
            <slot name="before-buttons" :file="file"/>
            <m-button @click="$emit('remove', file)"><i-mdi-close/></m-button>
            <slot name="after-buttons" :file="file"/>
        </m-flex>
    </m-content-block>
</template>

<script setup lang="ts">
import type { UploadFile } from '~/types/core';

defineProps<{
    file: UploadFile,
    paused?: boolean,
    pausedReason?: string
}>();

defineEmits(['remove']);
</script>