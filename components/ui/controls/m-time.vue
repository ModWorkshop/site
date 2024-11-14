<template>
    <span v-if="overrideText || !dateTimeHack" :title="titleHover" @mouseenter="mouseEntered">
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
import { differenceInMonths, differenceInSeconds, intlFormatDistance, parseISO } from 'date-fns';

const { datetime, timeStyle = 'short', dateStyle = 'short', relative, relativeTimeStyle = 'long' } = defineProps<{
    datetime?: string | Date;
    dateStyle?: false | 'full' | 'long' | 'medium' | 'short';
    timeStyle?: false | 'full' | 'long' | 'medium' | 'short';
    relativeTimeStyle?: Intl.RelativeTimeFormatStyle;
    relative?: boolean;
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

    if (relative) {
        if (secs < 60) {
            return t('just_now');
        } else {
            const diff = differenceInMonths(now.value, datetime);
            return intlFormatDistance(datetime, now.value, {
                locale: locale.value,
                numeric: 'always',
                unit: diff >= 1 && diff <= 12 ? 'month' : undefined, // Who uses quarters to count time?????
                style: relativeTimeStyle
            });
        }
    }
});

function mouseEntered() {
    if (datetime) {
        const formatter = new Intl.DateTimeFormat(locale.value);
        titleHover.value = formatter.format(typeof datetime == 'string' ? parseISO(datetime) : datetime);
    }
}
</script>