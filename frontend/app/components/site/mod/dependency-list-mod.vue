<template>
	<m-content-block v-if="url" gap="2" :column="false" alt-background class="cursor-pointer" @click="clickUrl">
		<NuxtLink :to="url">
			<mod-thumbnail :thumbnail="dep.mod?.thumbnail" style="height: 48px;"/>
		</NuxtLink>
		<m-flex column>
			<template v-if="dep.mod">
				<NuxtLink :to="`/mod/${dep.mod_id}`" target="_blank">
					{{ dep.mod.name }} <m-tag v-if="dep.optional">{{ $t('optional') }}</m-tag>
				</NuxtLink>
				<a-user avatar-size="xs" :user="dep.mod.user"/>
			</template>
			<template v-else>
				<NuxtLink :to="dep.url">
					{{ dep.name }} <m-tag v-if="dep.optional">{{ $t('optional') }}</m-tag>
				</NuxtLink>
				<span>
					{{ $t('offsite_mod') }}
				</span>
			</template>
		</m-flex>
	</m-content-block>
</template>

<script setup lang="ts">
import type { Dependency } from '~/types/models';

const { dep } = defineProps<{
	dep: Dependency;
}>();

const url = computed(() => dep.mod ? `/mod/${dep.mod_id}` : dep.url);

function clickUrl() {
	window.open(url.value, '_blank');
}
</script>
