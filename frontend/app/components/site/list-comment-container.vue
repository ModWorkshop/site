<template>
	<details v-if="comment.user?.blocked_by_me" :class="['flex', 'flex-col', 'content-block', 'p-2', isReply && 'alt-content-bg']">
		<summary class="p-4">
			{{ $t('blocked_user') }}
		</summary>
		<slot/>
	</details>
	<slot v-else/>
</template>

<script setup lang="ts">
import type { Comment } from '~/types/models';

const { comment, isReply } = defineProps<{
	comment: Comment;
	isReply?: boolean;
}>();
</script>
