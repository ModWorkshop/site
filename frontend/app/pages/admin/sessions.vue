<template>
	<m-flex column gap="3" style="flex: 1;">
		<h2>{{ $t('sessions') }}</h2>
		<m-input v-model="ipAddress" :label="$t('ip_address')" clearable/>
		<user-select v-model="user" :label="$t('user')" clearable/>
		<m-list v-model:page="page" query :items="sessions" :loading="loading">
			<template #before-item="{ item }">
				<a-user :user="item.user" class="overflow-hidden">
					<template #details>
						<span>{{ item.ip_address }}</span>
						<m-time :datetime="item.updated_at" relative/>
					</template>
				</a-user>
			</template>
		</m-list>
	</m-flex>
</template>

<script setup lang="ts">
import type { Game, TrackSession } from '~/types/models';

const props = defineProps<{
	game: Game;
}>();

useNeedsPermission('moderate-users', props.game);

const user = useRouteQuery('filter-user', null, 'number');
const ipAddress = ref('');
const page = ref(1);

const { data: sessions, loading } = await useFetchMany<TrackSession>('track-sessions', {
	query: {
		page,
		user_id: user,
		ip_address: ipAddress,
		limit: 20
	}
});
</script>
