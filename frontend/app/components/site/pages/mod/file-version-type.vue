<template>
	<div :class="['version-type', `version-type-${file.version_type}`]" :title="$t(`version_${file.version_type}`)">
		{{ versionTypeLetter }}
	</div>
</template>

<script setup lang="ts">
import type { File } from '~/types/models';

const { file } = defineProps<{
	file: File;
}>();
const versionTypeLetter = computed(() => file.version_type.substring(0, 1).toUpperCase());
</script>

<style scoped>
.version-type {
	margin-right: auto;
	margin-left: auto;
	border-radius: var(--content-border-radius);
	display: flex;
	align-items: center;
	justify-content: center;
	width: 32px;
	height: 32px;
}

.version-type-release {
	color: var(--success-color);
	background-color: var(--success-bg-color);
}

.version-type-beta {
	color: var(--warning-color);
	background-color: var(--warning-bg-color);

}

.version-type-alpha {
	color: #d48c8c;
	background-color: #5d3232;
}
</style>
