<template>
	<m-content-block
		gap="2"
		inline
		:class="{'items-center': true, 'cursor-pointer': true, 'no-background': !background}"
		:padding="1"
		:column="false"
		@click.self="clickFunc"
	>
		<NuxtLink :to="modUrl" style="height: 48px; aspect-ratio: 2/1;">
			<mod-thumbnail :thumbnail="mod?.thumbnail"/>
		</NuxtLink>
		<m-flex column gap="1" @click.self="clickFunc">
			<template v-if="mod">
				<NuxtLink :to="modUrl" class="line-clamp-2" style="word-break: break-all;">
					{{ mod.name }}
				</NuxtLink>
				<m-flex class="items-center" style="word-break: break-all;" wrap>
					<i18n-t v-if="showGame && mod.game" keypath="user_posted_place_no_date">
						<template #user>
							<a-user :user="mod.user" :static="static" avatar-size="xs" :show-mini-profile="false" class="self-start"/>
						</template>
						<template #place>
							<NuxtLink class="text-secondary" :to="!static && gameUrl || undefined" :title="mod.game.name">
								{{ mod.game.name }}
							</NuxtLink>
						</template>
					</i18n-t>

					<a-user v-else :user="mod.user" :static="static" avatar-size="xs" :show-mini-profile="false" class="self-start"/>
				</m-flex>
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

const { mod, static: stat, url, background = true, showGame = true } = defineProps<{
	mod?: Mod;
	url?: string;
	name?: string;
	static?: boolean;
	background?: boolean;
	showGame?: boolean;
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
