<template>
	<m-form v-model="userForm" float-save-gui autocomplete="off" :flush-changes="fc" @submit="save">
		<m-flex v-if="extra" gap="3" column>
			<m-input v-model="extra.auto_subscribe_to_mod" type="checkbox" :label="$t('auto_subscribe_to_mod')"/>
			<m-input v-model="extra.auto_subscribe_to_thread" type="checkbox" :label="$t('auto_subscribe_to_thread')"/>
			<m-input v-model="extra.developer_mode" type="checkbox" :label="$t('developer_mode')" :desc="$t('developer_mode_help')"/>
		</m-flex>
	</m-form>
</template>

<script setup lang="ts">
import type { User } from '~/types/models';
import clone from 'rfdc/default';
import { useStore } from '~/store';

const { user } = defineProps<{
	user: User;
}>();

const userForm = reactive({
	extra: clone(user.extra)
});

const isMe = inject<boolean>('isMe');
const { setUser } = useStore();
const showError = useQuickErrorToast();
const fc = createEventHook();

if (!isMe) {
	useNoPermsPage();
}

const extra = computed(() => userForm.extra);
async function save() {
	try {
		setUser(await patchRequest<User>(`users/${user.id}`, userForm));
		fc.trigger(userForm);
	} catch (error) {
		showError(error);
	}
}
</script>
