<template>
	<m-flex column>
		<m-progress
			:current="progress?.progress"
			:height="8"
			:show-text="false"
			:alt-background="altBackground"
		/>
		<small class="whitespace-pre-line">
			{{ $t('uploading_detailed', { current, total, speed, time }) }}
		</small>
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

const time = computed(() => huamnizeDuration(progress?.estimated ?? 0, durationFormat.value));
const speed = computed(() => friendlySize(progress?.rate ?? 0));
const current = computed(() => friendlySize(progress?.loaded ?? 0));
const total = computed(() => friendlySize(progress?.total ?? 0));
</script>
