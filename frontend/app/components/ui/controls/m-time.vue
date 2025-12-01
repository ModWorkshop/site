<template>
	<span v-if="overrideText || !dateTimeHack" :title="titleHover" @mouseenter="mouseEntered">
		{{ overrideText ?? $t('never') }}
	</span>
	<NuxtTime
		v-else
		:datetime="dateTimeHack"
		:date-style="dateStyle || undefined"
		:time-style="timeStyle || undefined"
		:title="titleHover"
		:locale="useSystemDateFormat ? undefined : locale"
		@mouseenter="mouseEntered"
	/>
</template>

<script setup lang="ts">
import { differenceInMonths, differenceInSeconds, parseISO } from 'date-fns';

const { text, datetime, timeStyle = 'short', dateStyle = 'short', relative, relativeTimeStyle = 'long' } = defineProps<{
	datetime?: string | Date;
	dateStyle?: false | 'full' | 'long' | 'medium' | 'short';
	timeStyle?: false | 'full' | 'long' | 'medium' | 'short';
	relativeTimeStyle?: Intl.RelativeTimeFormatStyle;
	relative?: boolean;
	text?: string;
}>();

const { locale, t } = useI18n();

const titleHover = ref();
const now = useNow();

const dateTimeHack = computed(() => {
	// eslint-disable-next-line @typescript-eslint/no-unused-expressions
	locale.value; // Just a hack to make it reload on language change
	return datetime;
});

const useRelativeTime = useConsentedCookie('use-relative-time', { default: () => true, expires: longExpiration() });
const useSystemDateFormat = useConsentedCookie('use-system-date-format');

const overrideText = computed(() => {
	if (text) {
		return text;
	}

	if (!datetime) {
		return;
	}

	const date = typeof datetime === 'string' ? parseISO(datetime) : datetime;
	const secs = Math.abs(differenceInSeconds(now.value, date));

	if (relative && useRelativeTime.value) {
		if (secs < 60) {
			return t('just_now');
		} else {
			const diffInHours = Math.floor(secs / 3600);
			const diffInDays = Math.floor(secs / 86400);
			const diffInMonths = differenceInMonths(now.value, date);

			const rtf = new Intl.RelativeTimeFormat(locale.value, {
				numeric: 'always',
				style: relativeTimeStyle
			});

			if (diffInMonths >= 12) {
				const years = Math.floor(diffInMonths / 12);
				return rtf.format(-years, 'year');
			} else if (diffInMonths >= 1) {
				return rtf.format(-diffInMonths, 'month');
			} else if (diffInDays >= 1) {
				return rtf.format(-diffInDays, 'day');
			} else if (diffInHours >= 1) {
				return rtf.format(-diffInHours, 'hour');
			} else {
				const diffInMinutes = Math.floor(secs / 60);
				return rtf.format(-diffInMinutes, 'minute');
			}
		}
	}
});

function mouseEntered() {
	if (datetime) {
		const formatter = new Intl.DateTimeFormat(locale.value, {
			timeStyle: 'short',
			dateStyle: 'long'
		});

		titleHover.value = formatter.format(typeof datetime === 'string' ? parseISO(datetime) : datetime);
	}
}
</script>
