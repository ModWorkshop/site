<template>
    <span :title="fullDateStr">{{timeAgoStr}}</span>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { fullDate, getTimeAgo } from '~~/utils/helpers';

const props = defineProps({
    time: String
});

const { t } = useI18n();

const timeAgoStr = computed(() => {
    if (props.time) {
        return getTimeAgo(props.time);        
    } else {
        return t('never');
    }
});
const fullDateStr = computed(() => props.time ? fullDate(props.time) : undefined);
</script>