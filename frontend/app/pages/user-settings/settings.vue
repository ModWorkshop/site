<template>
	<m-flex v-if="extra" gap="3" column>
		<m-input v-model="extra.auto_subscribe_to_mod" type="checkbox" :label="$t('auto_subscribe_to_mod')"/>
		<m-input v-model="extra.auto_subscribe_to_thread" type="checkbox" :label="$t('auto_subscribe_to_thread')"/>
		<m-input v-model="extra.developer_mode" type="checkbox" :label="$t('developer_mode')" :desc="$t('developer_mode_help')"/>
	</m-flex>
</template>

<script setup lang="ts">
import type { User } from '~/types/models';

const { user } = defineProps<{
	user: User;
}>();

const extra = computed(() => user.extra);
</script>
