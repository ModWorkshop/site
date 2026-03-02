<template>
	<m-list
		url="supporter-packages"
		query
		:item-link="item => `${url}/${item.id}`"
		:new-button="`${url}/new`"
	>
		<template #item-buttons="{ item }">
			<m-button @click.prevent="$router.push(`/supporter-packages/${item.id}`)"><i-mdi-launch/></m-button>
		</template>
	</m-list>
</template>

<script setup lang="ts">
useNeedsPermission('admin');

const url = computed(() => getAdminUrl('supporter-packages'));
</script>
