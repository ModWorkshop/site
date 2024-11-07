<template>
    <NuxtTime 
        v-if="dateTimeHack"
        :datetime="dateTimeHack"
        :date-style="dateStyle || undefined"
        :time-style="timeStyle || undefined"
        @mouseenter="mouseEntered"
        :title="titleHover"
        :locale="locale"
    />
    <span v-else>
        {{$t('never')}}
    </span>
</template>

<script setup lang="ts">
import { parseISO } from 'date-fns';

const { datetime, timeStyle = 'short', dateStyle = 'short' } = defineProps<{
    datetime?: string | number | Date;
    dateStyle?: false | 'full' | 'long' | 'medium' | 'short';
    timeStyle?: false | 'full' | 'long' | 'medium' | 'short';
}>();

const { locale } = useI18n();

const titleHover = ref();

const dateTimeHack = computed(() => {
    locale.value; //Just a hack to make it reload on language change
    return datetime;
})

function mouseEntered() {
    if (datetime) {
        const formatter = new Intl.DateTimeFormat(locale.value);
        titleHover.value = formatter.format(typeof datetime == 'string' ? parseISO(datetime) : datetime);
    }
}
</script>