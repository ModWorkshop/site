<template>
	<m-flex gap="4" class="my-2" column>
		<m-flex class="items-center">
			<m-link class="h2" style="font-weight: 800;" :to="`/g/${game.short_name}`">{{ game.name }}</m-link>
		</m-flex>

		<m-content-block :column="false" wrap class="content-block-glass" gap="5" padding="5">
			<m-flex wrap gap="4">
				<m-link v-if="!store.isBanned" v-once :to="user ? `/g/${game.short_name}/upload` : '/login'">
					<i-mdi-upload/> {{ $t('upload_mod') }}
				</m-link>
				<m-link :to="`/g/${game.short_name}/mods`"><i-mdi-puzzle/> {{ $t('mods') }}</m-link>
				<m-link :to="`/g/${game.short_name}/forum`"><i-mdi-forum/> {{ $t('forum') }}</m-link>
				<m-link v-if="store.user" :to="`/g/${game.short_name ?? game.id}/user/${store.user.id}`" :class="{ 'max-md:hidden': canSeeAdminGamePage || buttons }">
					<i-mdi-account-settings-variant/> {{ $t('game_settings') }}
				</m-link>
				<m-dropdown v-if="canSeeAdminGamePage || buttons">
					<m-link>{{ $t('more') }} <i-mdi-chevron-down/></m-link>
					<template #content>
						<m-dropdown-item v-if="store.user" :to="`/g/${game.short_name ?? game.id}/user/${store.user.id}`" class="md:!hidden">
							<i-mdi-account-settings-variant/> {{ $t('game_settings') }}
						</m-dropdown-item>
						<m-dropdown-item v-if="canSeeAdminGamePage" :to="`/g/${game.short_name}/admin`"><i-mdi-cogs/> {{ $t('admin_page') }}</m-dropdown-item>
						<m-dropdown-item v-for="button in buttons" :key="button[0]" class="nav-item" :href="button[1]"><i-mdi-link-variant/> {{ button[0] }}</m-dropdown-item>
					</template>
				</m-dropdown>
			</m-flex>
			<m-flex class="sm:ml-auto" gap="4">
				<m-flex v-if="store.gameBan" v-once>
					<span class="text-danger">
						<i-mdi-alert/> {{ $t('banned') }}
					</span>
					<span>
						(<i18n-t keypath="expires_t" scope="global">
							<template #time>
								<m-time :datetime="store.gameBan?.expire_date" relative/>
							</template>
						</i18n-t>)
					</span>
				</m-flex>

				<m-flex gap="2">
					<NuxtLink v-if="canSeeReports" :title="$t('reports')" :class="{ 'text-warning': hasReports, 'text-body': !hasReports }" :to="`/g/${game.short_name}/admin/reports`">
						<i-mdi-alert-box/> {{ reportCount }}
					</NuxtLink>
					<NuxtLink v-if="canSeeWaiting" :title="$t('approvals')" :class="{ 'text-warning': hasWaiting, 'text-body': !hasWaiting }" :to="`/g/${game.short_name}/admin/approvals`">
						<i-mdi-clock/> {{ waitingCount }}
					</NuxtLink>
				</m-flex>
				<m-link v-if="store.user" style="white-space: pre;" :to="!store.user && '/login'" @click="store.user && setFollowGame(game!)">
					<i-mdi-minus-thick v-if="game.followed"/>
					<i-mdi-plus-thick v-else/>
					{{ $t(game.followed ? 'unfollow' : 'follow') }}
				</m-link>
				<m-dropdown v-if="store.user" align="end" dropdown-class="user-dropdown">
					<i-mdi-dots-vertical class="hover:cursor-pointer"/>
					<template #content>
						<m-dropdown-item v-if="store.user" @click="setIgnoreGame(game!)">
							<i-mdi-eye v-if="game.ignored"/>
							<i-mdi-eye-off v-else/>
							{{ $t(game.ignored ? 'unignore' : 'ignore') }}
						</m-dropdown-item>
					</template>
				</m-dropdown>
			</m-flex>
		</m-content-block>
	</m-flex>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { Game } from '~/types/models';

const { game } = defineProps<{
	game: Game;
}>();

const store = useStore();
const { user } = storeToRefs(store);

const reportCount = computed(() => game!.report_count ?? 0);
const waitingCount = computed(() => game!.waiting_count ?? 0);
const hasReports = computed(() => reportCount.value > 0);
const hasWaiting = computed(() => waitingCount.value > 0);
const canSeeReports = computed(() => store.hasPermission('manage-users', game));
const canSeeWaiting = computed(() => store.hasPermission('manage-mods', game));

const canSeeAdminGamePage = computed(() => game && adminGamePagePerms.some(perm => store.hasPermission(perm, game)));

const buttons = computed(() => {
	if (game && game.buttons) {
		const btns = game.buttons.split(',');
		const res: string[][] = [];

		for (const btn of btns) {
			res.push(btn.split('|'));
		}

		return res;
	}
});
</script>

<style scoped>

</style>
