<template>
	<m-content-block alt-background>
		<details class="audit-log" :open="open">
			<summary class="audit-summmary" @click.prevent="open = hasContent && !open">
				<m-button v-if="admin" class="danger-button my-auto">
					<i-mdi-delete @click.stop="deleteModLog(log, logs)"/>
				</m-button>
				<m-flex class="my-auto" gap="0">
					<a-user :user="log.user" :name="false" class="m-auto"/>
					<m-flex column class="w-full my-auto">
						<i18n-t :keypath="`log_${log.type}`" tag="span" style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
							<template #user>
								<a-user :user="log.user" :avatar="false"/>
							</template>
							<template #context>
								<a-user v-if="log.context_type == 'user'" :user="log.context as User"/>
								<span v-else>"{{ log.context_name ?? 'Unknown' }}"</span>
							</template>
							<template #auditable>
								<a-user v-if="log.auditable_type == 'user'" :user="log.auditable as User" :avatar="false"/>
								<span v-else-if="log.auditable_name || log.auditable">"{{ log.auditable_name ?? 'Unknown' }}"</span>
							</template>
							<template #context_type>
								{{ pretty(log.context_type) }}
							</template>
							<template #auditable_type>
								{{ pretty(log.auditable_type) }}
							</template>
							<template v-if="log.context" #associated_context>
								Associated to
								<a-user v-if="log.context_type == 'user'" :user="log.context as User"/>
								<span v-else>"{{ log.context_name ?? 'Unknown' }}"</span>
							</template>
							<template #custom>
								{{ custom }}
							</template>
						</i18n-t>
					</m-flex>
				</m-flex>
				<div v-if="hasContent" class="text-2xl ml-auto my-auto">
					<i-mdi-chevron-down v-if="open"/>
					<i-mdi-chevron-right v-else/>
				</div>
			</summary>

			<div v-if="hasContent" class="audit-details">
				<m-flex v-if="log.data.changes && Object.entries(log.data.changes).length" column gap="3">
					<m-flex column>
						<span v-for="(change, key) in log.data.changes" :key="key">
							{{ key+1 }}.
							<template v-if="change.type == 'set'">
								{{ pretty(change.key) }}: "{{ change.old_value }}" <i-mdi-arrow-right-thin/> "{{ change.value }}"
							</template>
							<template v-if="change.type == 'remove'">
								Removed {{ pretty(change.key) }}: {{ change.value_name }}
							</template>
							<template v-if="change.type == 'add'">
								Added {{ pretty(change.key) }}: {{ change.value_name }}
							</template>
						</span>
					</m-flex>
				</m-flex>
				<m-flex v-if="customDetails">
					{{ customDetails }}
				</m-flex>
			</div>
		</details>
		<small class="text-secondary mt-auto">
			<m-time :datetime="log.updated_at" relative/>
		</small>
	</m-content-block>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import { useStore } from '~/store';
import type { AuditLog, User } from '~/types/models';

const { log } = defineProps<{
	log: AuditLog;
	logs: AuditLog[];
}>();

const showDialog = useQuickErrorToast();
const { hasPermission } = useStore();

const open = ref(false);
const hasContent = computed(() => {
	if (!log.data) {
		return false;
	}

	return !!log.data.changes || customDetails.value;
});

const admin = computed(() => hasPermission('admin'));

// @ts-expect-error The errors are wrong...
useI18n({
	messages: {
		en: {
			log_ban: '{user} banned {auditable} {custom}',
			log_delete: '{user} deleted {auditable_type} {auditable} {associated_context}',
			log_update: '{user} updated {auditable_type} {auditable}',
			log_create: '{user} created {auditable_type} {auditable}',
			log_mod_approve_status: '{user} has {custom} {auditable}',
			log_mod_suspend_status: '{user} has {custom} {auditable}',
			log_category_mass_move_mods: '{user} moved {auditable} mods to {context}'
		}
	}
});

const custom = computed(() => {
	if (log.type == 'ban') {
		return log.data.with.expire_date ? `until ${log.data.with.expire_date}` : 'permanently';
	} else if (log.type == 'mod_approve_status') {
		return log.data.status ? 'approved' : 'rejected';
	} else if (log.type == 'mod_suspend_status') {
		return log.data.status ? 'suspended' : 'unsuspended';
	}
});

const customDetails = computed(() => {
	if (log.type == 'mod_approve_status' || log.type == 'mod_suspend_status') {
		return 'Reason: ' + log.data.reason;
	}
});

async function deleteModLog(item: AuditLog, items: AuditLog[]) {
	try {
		await deleteRequest(`audit-logs/${item.id}`);
		remove(items, item);
	} catch (e) {
		showDialog(e);
	}
}
</script>

<style scoped>
.audit-details {
	margin-top: 1rem;
}
.audit-summmary {
	display: flex;
	gap: 1rem;
	&::marker {
		content: '';
	}
}
</style>
