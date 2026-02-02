<template>
	<m-flex column>
		<m-flex :gap="3" :class="{ 'flex-col': true, 'md:flex-row': !column }">
			<m-content-block grow :class="{ 'self-auto': true, 'md:self-start': !column }" style="flex: 1;" :padding="column ? 0 : 4">
				<m-input v-model="query" :label="$t('search')"/>
			</m-content-block>
			<m-flex grow style="flex: 4;" gap="1">
				<m-list v-model:page="page" query :items="users" :loading="loading">
					<template #item="{ item }">
						<m-content-block :key="item.id" class="cursor-pointer items-center" :column="false" :alt-background="altBackground" :to="`g${item.short_name}`">
							<game-thumbnail :game="item" style="width: 100px;"/>
							<m-flex column gap="2">
								<span>{{ item.name }}</span>
								<span v-if="item.mods_count || item.mods_count === 0" class="text-secondary">{{ $t('mod_count', { n: item.mods_count }) }}</span>
							</m-flex>
						</m-content-block>
					</template>
				</m-list>
			</m-flex>
		</m-flex>
	</m-flex>
</template>

<script setup lang="ts">
import type { User, Game } from '~/types/models';

const page = ref(1);
const query = useRouteQuery('query', '');
const searchBus = useEventBus<string>('search');

searchBus.on(search => query.value = search);

const { column = false, altBackground = false } = defineProps<{
	userLink?: (user: User) => string;
	column?: boolean;
	altBackground?: boolean;
	url?: string;
	game?: Game;
}>();

const { data: users, loading } = await useWatchedFetchMany<User>('games', {
	page,
	query,
	including_ignored: true
});
</script>
