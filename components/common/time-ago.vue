<template>
    <span :title="fullDateStr">{{timeAgoStr}}</span>
</template>

<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
    time: String
});

const { t } = useI18n();

const timeAgoStr = computed(() => {
    if (props.time) {
        return timeAgo(props.time);        
    } else {
        return t('never');
    }
});
const fullDateStr = computed(() => props.time ? fullDate(props.time) : null);
</script>