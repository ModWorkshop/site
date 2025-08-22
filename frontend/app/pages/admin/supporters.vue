<template>
	<m-flex column gap="3">
		<h2>{{ $t('upgrade_user') }}</h2>
		<user-select v-model="user" :label="$t('user')"/>
		<m-duration v-model="duration" :label="$t('duration')"/>
		<m-button class="mr-auto" :disabled="!user" @click="upgrade">{{ $t('upgrade') }}</m-button>

		<h2>{{ $t('supporters') }}</h2>
		<m-list v-model:page="page" query :items="supporters" :loading="loading">
			<template #item="{ item }">
				<m-flex class="list-button" :style="{ opacity: item.expired ? 0.5 : 1 }">
					<m-flex column>
						<a-user :user="item.user"/>
						<div>
							{{ $t('date') }}: <m-time :datetime="item.created_at" relative/>
						</div>
						<div v-if="!item.expired">
							{{ $t('expires') }}: <m-time :datetime="item.expire_date" relative/>
						</div>
						<div v-else>
							{{ $t('expired') }}
						</div>
					</m-flex>
					<m-button class="ml-auto self-center" @click="removeSupporter(item)"><i-mdi-stop/> {{ $t('stop') }}</m-button>
				</m-flex>
			</template>
		</m-list>
	</m-flex>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import { useI18n } from 'vue-i18n';
import type { Supporter } from '~/types/models';

useNeedsPermission('manage-users');

const user = ref(null);
const duration = ref(null);
const page = ref(1);

const yesNoModal = useYesNoModal();
const { showToast } = useToaster();
const { t } = useI18n();

const { data: supporters, loading } = await useWatchedFetchMany<Supporter>('supporters', { page });

async function upgrade() {
	try {
		const supporter = await postRequest<Supporter>('supporters', {
			user_id: user.value,
			expire_date: duration.value
		});
		supporters.value?.data.unshift(supporter);
	} catch {
		showToast({
			color: 'danger',
			desc: t('could_not_upgrade_user')
		});
	}
}

function removeSupporter(supporter: Supporter) {
	yesNoModal({
		title: t('stop_supporter_status'),
		desc: t('stop_supporter_status_desc'),
		async yes() {
			await deleteRequest(`supporters/${supporter.id}`);
			if (supporters.value?.data) {
				remove(supporters.value.data, supporter);
			}
		}
	});
}
</script>
