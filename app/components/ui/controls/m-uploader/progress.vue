<template>
    <m-flex column>
        <m-progress 
            :current="progress?.progress"
            :height="8"
            :show-text="false"
            style="width: 250px;"
            :alt-background="altBackground"
        />
        {{$t('uploading_detailed', { current, total, speed, time })}}
    </m-flex>
</template>

<script setup lang="ts">
import type { AxiosProgressEvent } from 'axios';
import humanizeDuration from 'humanize-duration';

const { progress } = defineProps<{ 
    progress: AxiosProgressEvent;
    altBackground?: boolean;
}>();

const time = computed(() => humanizeDuration(Math.ceil(progress?.estimated ?? 0) * 1000));
const speed = computed(() => friendlySize(progress?.rate ?? 0));
const current = computed(() => friendlySize(progress?.bytes ?? 0));
const total = computed(() => friendlySize(progress?.total ?? 0));
</script>