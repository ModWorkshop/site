<template>
	<m-content-block gap="2" inline :class="{'items-center': true, 'cursor-pointer': true, 'no-background': !background}" :column="false" @click.self="clickFunc">
		<NuxtLink :to="modUrl" style="flex: 1;">
			<mod-thumbnail :thumbnail="mod?.thumbnail"/>
		</NuxtLink>
		<m-flex column gap="2" style="flex: 2;" @click.self="clickFunc">
			<template v-if="mod">
				<NuxtLink :to="modUrl" class="line-clamp-2">
					{{ mod.name }}
				</NuxtLink>
				<a-user :user="mod.user" :static="static" avatar-size="xs" :show-mini-profile="false" class="self-start"/>
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
	</m-content-block>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { Mod } from '~/types/models';

const { mod, static: stat, url, background = true } = defineProps<{
	mod?: Mod;
	url?: string;
	name?: string;
	static?: boolean;
	background?: boolean;
}>();

const store = useStore();
const router = useRouter();

const game = computed(() => mod?.game);
const gameUrl = computed(() => `/g/${game.value?.short_name || store.currentGame?.short_name || game.value?.short_name || game.value?.id}`);

const modUrl = computed(() => {
	if (!stat) {
		return url ?? `/mod/${mod?.id}`;
	}
});

function clickFunc() {
	if (modUrl.value && !stat) {
		router.push(modUrl.value);
	}
}
</script>

<style>
.no-background {
	box-shadow: none !important;
	background: none !important;
}
</style>
