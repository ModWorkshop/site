<template>
	<Teleport to="body">
		<Transition>
			<m-form-modal v-if="showModal" v-model="showModal" :title="approve ? $t('approve') : $t('reject')" @submit="submit">
				<m-input v-if="!approve" v-model="form.reason" :label="$t('reason')" type="textarea" rows="6"/>
				<m-input v-model="form.notify" :label="$t('notify_owner_members')" type="checkbox"/>
			</m-form-modal>
		</Transition>
	</Teleport>
	<m-flex>
		<div @click="showModal = true; approve = true;">
			<slot>
				<m-button>{{ $t('approve') }}</m-button>
			</slot>
		</div>
		<div v-if="mod.approved === null" @click="showModal = true; approve = false;">
			<slot>
				<m-button>{{ $t('reject') }}</m-button>
			</slot>
		</div>
	</m-flex>
</template>

<script setup lang="ts">
import type { Mod } from '~/types/models';
import { remove } from '@antfu/utils';

const props = defineProps<{
	mod: Mod;
	mods?: Mod[];
}>();

const emit = defineEmits<{
	(e: 'approved', status: boolean): void;
}>();

const approve = ref(false);
const showModal = ref(false);
const form = reactive({
	status: approve,
	reason: '',
	notify: true
});

async function submit(onError) {
	try {
		await patchRequest(`mods/${props.mod.id}/approved`, form);
		props.mod.approved = approve.value;
		form.reason = '';
		showModal.value = false;

		emit('approved', approve.value);

		if (props.mods) {
			remove(props.mods, props.mod);
		}
	} catch (error) {
		onError(error);
	}
}
</script>
