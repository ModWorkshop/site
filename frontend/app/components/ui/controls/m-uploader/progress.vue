<template>
	<m-flex column>
		<m-progress
			:current="progress?.progress"
			:height="8"
			:show-text="false"
			:alt-background="altBackground"
		/>
		<span class="whitespace-pre-line">
			{{ $t('uploading_detailed', { current, total, speed, time }) }}
		</span>
	</m-flex>
</template>

<script setup lang="ts">
import type { AxiosProgressEvent } from 'axios';

const { progress } = defineProps<{
	progress: AxiosProgressEvent;
	altBackground?: boolean;
}>();

const { locale } = useI18n();
const durationFormat = computed(() => new Intl.DurationFormat(locale.value, { style: 'narrow', secondsDisplay: 'always' }));

const time = computed(() => {
	const seconds = Math.round(progress?.estimated ?? 0);
	const minutes = Math.round(seconds / 60);
	const hours = Math.round(minutes / 60);

	if (hours >= 1) {
		return durationFormat.value.format({ hours });
	} else if (minutes >= 1) {
		return durationFormat.value.format({ minutes });
	}

	return durationFormat.value.format({ seconds });
});
const speed = computed(() => friendlySize(progress?.rate ?? 0));
const current = computed(() => friendlySize(progress?.bytes ?? 0));
const total = computed(() => friendlySize(progress?.total ?? 0));
</script>
