<template>
	<Transition name="left-slide">
		<m-flex v-show="!side || menuOpen" :class="{ 'nav-menu': true, 'p-6': side }" :column="side">
			<slot/>
		</m-flex>
	</Transition>
</template>

<script setup lang="ts">
const { side = false, root } = defineProps<{
	side?: boolean;
	root?: string;
}>();

const menuOpen = defineModel<boolean>('menuOpen');

provide('root', root);
provide('side', side);
provide('menuOpen', menuOpen.value);
</script>
