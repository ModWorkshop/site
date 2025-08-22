<template>
	<span v-if="adminData" class="h2">{{ $t('admin_at_a_glance') }}</span>
	<m-flex v-if="adminData" class="max-md:flex-wrap">
		<m-flex class="flex-1" column>
			<NuxtLink :to="reportsUrl" class="glance-block">
				<span class="text-2xl text-body">{{ $t('last_reports') }}</span>
				<m-flex v-if="adminData.reports" column>
					<template v-if="adminData.reports.length">
						<m-content-block v-for="report of adminData?.reports" :key="report.id" class="text-body">
							{{ report.reason }}
						</m-content-block>
					</template>
					<span v-else class="text-body">
						{{ $t('nothing_found') }}
					</span>
				</m-flex>
			</NuxtLink>

			<NuxtLink :to="casesUrl" class="glance-block">
				<span class="text-2xl text-body">{{ $t('last_cases') }}</span>
				<m-flex v-if="adminData.user_cases" column>
					<template v-if="adminData.user_cases.length">
						<m-content-block v-for="userCase of adminData?.user_cases" :key="userCase.id" class="text-body">
							<a-user :user="userCase.user" avatar-size="xs"/>
							<span>{{ $t('reason') }}: {{ userCase.reason }}</span>
						</m-content-block>
					</template>
					<span v-else class="text-body">
						{{ $t('nothing_found') }}
					</span>
				</m-flex>
			</NuxtLink>
		</m-flex>

		<m-flex class="flex-1" column>
			<NuxtLink :to="susUrl" class="glance-block">
				<span class="text-2xl text-body">{{ $t('last_suspensions') }}</span>
				<m-flex v-if="adminData.suspensions" column>
					<template v-if="adminData.suspensions.length">
						<m-content-block v-for="sus of adminData.suspensions" :key="sus.id" class="text-body" :column="false">
							<mod-thumbnail :thumbnail="sus.mod.thumbnail" style="width: 96px; height: 48px;"/>
							<m-flex column>
								<span>{{ sus.mod.name }}</span>
								<span>{{ $t('reason') }}: {{ sus.reason }}</span>
							</m-flex>
						</m-content-block>
					</template>
					<span v-else class="text-body">
						{{ $t('nothing_found') }}
					</span>
				</m-flex>
			</NuxtLink>

			<NuxtLink :to="bansUrl" class="glance-block">
				<span class="text-2xl text-body">{{ $t('last_bans') }}</span>
				<m-flex v-if="adminData.bans" column>
					<template v-if="adminData.bans.length">
						<m-content-block v-for="ban of adminData.bans" :key="ban.id" class="text-body">
							<a-user :user="ban.user" avatar-size="xs"/>
							<span>{{ $t('reason') }}: {{ ban.reason }}</span>
						</m-content-block>
					</template>
					<span v-else class="text-body">
						{{ $t('nothing_found') }}
					</span>
				</m-flex>
			</NuxtLink>
		</m-flex>
	</m-flex>
</template>

<script setup lang="ts">
import type { Ban, Game, Report, Suspension, UserCase } from '~/types/models';

const { game } = defineProps<{
	game?: Game;
}>();

const url = computed(() => getGameResourceUrl('admin-data', game));
const casesUrl = computed(() => getAdminUrl('cases', game));
const bansUrl = computed(() => getAdminUrl('bans', game));
const susUrl = computed(() => getAdminUrl('suspensions', game));
const reportsUrl = computed(() => getAdminUrl('reports', game));

const { data: adminData } = await useFetchData<{
	suspensions?: Suspension[];
	user_cases?: UserCase[];
	reports?: Report[];
	bans?: Ban[];
}>(url.value, { params: { all: true, limit: 3 } });
</script>

<style scoped>
.glance-block {
	display: flex;
	flex-direction: column;
	gap: 1rem;
	padding: 1rem;
	background-color: var(--alt-content-bg-color);
	border-radius: var(--content-border-radius);
}
</style>
