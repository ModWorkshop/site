<template>
	<m-content-block alt-background :column="false" wrap>
		<slot name="before-info" :file="file"/>
		<m-flex column class="break-words overflow-hidden">
			<span>{{ file.name }} ({{ friendlySize(file.size) }})</span>
			<span v-if="paused">{{ pausedReason ?? $t('waiting') }}</span>
			<m-time v-else-if="file.created_at" :datetime="file.created_at"/>
			<m-uploader-progress v-else-if="file.progress" :progress="file.progress"/>
			<span v-else>{{ $t('waiting') }}</span>
		</m-flex>
		<slot name="after-info" :file="file"/>
		<m-flex class="ml-auto items-center">
			<slot name="before-buttons" :file="file"/>
			<m-button color="danger" :disabled="!file.progress && !file.id" @click="$emit('remove', file)"><i-mdi-delete/></m-button>
			<slot name="after-buttons" :file="file"/>
		</m-flex>
	</m-content-block>
</template>

<script setup lang="ts">
import type { UploadFile } from '~/types/core';

const { file } = defineProps<{
	file: UploadFile;
	paused?: boolean;
	pausedReason?: string;
}>();

defineEmits(['remove']);
</script>
