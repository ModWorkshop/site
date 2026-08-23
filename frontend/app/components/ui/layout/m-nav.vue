<template>
	<div class="nav">
		<m-flex v-if="side" class="items-center lg:hidden" @click="menuOpen = !menuOpen">
			<m-link class="collapse-button">
				<i-mdi-menu/>
			</m-link>
		</m-flex>
		<m-flex :class="{ 'menu-open': menuOpen }" :column="!side" :gap="3">
			<div v-if="menuOpen" class="menu-closer" @click.prevent="menuOpen = false"/>
			<m-nav-menu v-model:menu-open="menuOpen" :side="side" :root="root">
				<slot/>
			</m-nav-menu>
			<m-flex column grow gap="3" :class="{ 'content-block': background, 'p-6': side, 'overflow-x-auto': true }">
				<slot name="content"/>
			</m-flex>
		</m-flex>
	</div>
</template>

<script setup lang="ts">
const { side = false, background = true, root } = defineProps<{
	side?: boolean;
	root?: string;
	padding?: string | number;
	background?: boolean;
}>();

const menuOpen = ref(false);
</script>

<style scoped>
.nav {
    display: flex;
    grid-gap: 12px;
    gap: 12px;
    overflow-x: hidden;
    flex-grow: 1;
    flex-direction: column;
}
</style>
