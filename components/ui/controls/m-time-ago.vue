<template>
    <ClientOnly>
        <template #fallback>
            <span v-bind="$attrs">{{timeAgoStr}}</span>
        </template>

        <span v-bind="$attrs" :title="fullDateStr">{{timeAgoStr}}</span>
    </ClientOnly>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { fullDate, getTimeAgo } from '~~/utils/helpers';

const props = defineProps<{
    time?: string
}>();

const { t, locale } = useI18n();

const timeAgoStr = computed(() => {
    locale.value; //Just a hack to make it reload on language change

    if (props.time) {
        return getTimeAgo(t, props.time);        
    } else {
        return t('never');
    }
});

const fullDateStr = computed(() => {
    locale.value;

    return props.time ? fullDate(props.time) : undefined;
});
</script>