<template>
	<page-block>
		<mod-list side-filters query url="mods/liked">
			<template #sort-buttons="{ sortBy, setSortBy }">
				<m-button :color="sortBy == 'liked_at' ? 'primary' : 'secondary'" @click="setSortBy('liked_at')">
					<i-mdi-heart/> {{ $t('liked_at') }}
				</m-button>
			</template>
		</mod-list>
	</page-block>
</template>

<script setup>
definePageMeta({
	middleware: 'users-only'
});
</script>
