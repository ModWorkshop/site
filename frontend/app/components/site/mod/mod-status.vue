<template>
	<m-icon v-if="status" :icon="status[0]" :class="status[2] ?? 'text-secondary'" :title="$t(status[1])"/>
</template>

<script setup lang="ts">
import type { Mod } from '~/types/models';
import MdiCancel from '~icons/mdi/cancel';
import MdiCloseThick from '~icons/mdi/close-thick';
import MdiClock from '~icons/mdi/clock';
import MdiEyeOff from '~icons/mdi/eye-off';
import MdiNewspaperRemove from '~icons/mdi/newspaper-remove';
import MdiDownloadOff from '~icons/mdi/download-off';
import MdiLock from '~icons/mdi/lock';

const props = defineProps<{
	mod: Mod;
}>();

const status = computed<[Component, string, string?] | null>(() => {
	const mod = props.mod;

	if (mod.suspended) {
		return [MdiCancel, 'suspended', 'text-danger'];
	} else if (mod.approved === null) {
		return [MdiClock, 'mod_waiting', 'text-warning'];
	} else if (mod.approved === false) {
		return [MdiCloseThick, 'mod_rejected', 'text-danger'];
	} else if (!mod.has_download) {
		return [MdiDownloadOff, 'no_downloads', 'text-warning'];
	} else if (mod.visibility == 'public' && !mod.published_at) {
		return [MdiNewspaperRemove, 'not_published', 'text-warning'];
	} else if (mod.visibility == 'unlisted') {
		return [MdiEyeOff, 'unlisted'];
	} else if (mod.visibility == 'private') {
		return [MdiLock, 'private'];
	}

	return null;
});
</script>
