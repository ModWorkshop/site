<template>
	<m-flex gap="2" inline class="items-center cursor-pointer" @click="modUrl ? $router.push(modUrl) : undefined">
		<NuxtLink :to="modUrl" style="flex: 1;">
			<mod-thumbnail :thumbnail="mod?.thumbnail"/>
		</NuxtLink>
		<m-flex column gap="2" style="flex: 2;">
			<template v-if="mod">
				<NuxtLink :to="modUrl">
					{{ mod.name }}
				</NuxtLink>
				<a-user :user="mod.user" :static="static" avatar-size="xs" :show-mini-profile="false" @click.stop/>
				<NuxtLink v-if="mod.game" class="text-secondary" :to="!static && gameUrl || undefined" :title="mod.game.name">
					{{ mod.game.name }}
				</NuxtLink>
			</template>
			<template v-else>
				<NuxtLink :to="url">
					{{ name }}
				</NuxtLink>
			</template>
		</m-flex>
	</m-flex>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { Mod } from '~/types/models';

const props = defineProps<{
	mod?: Mod;
	url?: string;
	name?: string;
	static?: boolean;
}>();

const store = useStore();

const game = computed(() => props.mod?.game);
const gameUrl = computed(() => `/g/${game.value?.short_name || store.currentGame?.short_name || game.value?.short_name || game.value?.id}`);

const modUrl = computed(() => {
	if (!props.static) {
		return props.url ?? `/mod/${props.mod?.id}`;
	}
});
</script>
