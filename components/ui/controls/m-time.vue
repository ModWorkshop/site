<template>
    <span v-if="overrideText || !dateTimeHack">
        {{overrideText ?? $t('never')}}
    </span>
    <NuxtTime 
        v-else
        :datetime="dateTimeHack"
        :date-style="dateStyle || undefined"
        :time-style="timeStyle || undefined"
        @mouseenter="mouseEntered"
        :title="titleHover"
        :locale="locale"
    />
</template>

<script setup lang="ts">
import { differenceInSeconds, parseISO } from 'date-fns';

const { datetime, timeStyle = 'short', dateStyle = 'short' } = defineProps<{
    datetime?: string | Date;
    dateStyle?: false | 'full' | 'long' | 'medium' | 'short';
    timeStyle?: false | 'full' | 'long' | 'medium' | 'short';
    
}>();

const { locale, t } = useI18n();

const titleHover = ref();
const now = useNow();

const dateTimeHack = computed(() => {
    locale.value; //Just a hack to make it reload on language change
    return datetime;
})

const overrideText = computed(() => {
    if (!datetime) {
        return;
    }

    const date = typeof datetime == 'string' ? parseISO(datetime) : datetime;
    const secs = Math.abs(differenceInSeconds(now.value, date));

    if (secs < 60) {
        return t('just_now');
    }
});

function mouseEntered() {
    if (datetime) {
        const formatter = new Intl.DateTimeFormat(locale.value);
        titleHover.value = formatter.format(typeof datetime == 'string' ? parseISO(datetime) : datetime);
    }
}
</script>