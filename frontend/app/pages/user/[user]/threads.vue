<template>
	<div>
		<Title>{{ $t('threads') }}</Title>
		<thread-list no-pins :user-id="user.id"/>
	</div>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { User } from '~/types/models';

const { user: me } = useStore();

const { user } = defineProps<{
	user: User;
}>();

if (user.id !== me?.id) {
	useNeedsPermission('manage-discussions');
}
</script>
