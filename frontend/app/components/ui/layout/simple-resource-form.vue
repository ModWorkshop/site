<template>
	<m-form
		v-model="vm"
		:created="vm && !!vm.id"
		float-save-gui
		:can-save="canSave"
		:flush-changes="fc"
		@submit="submit"
	>
		<m-flex column gap="3">
			<slot/>
			<m-alert v-if="deleteButton && vm.id" class="w-full" :title="$t('danger_zone')" color="danger">
				<m-flex gap="2">
					<slot name="danger-zone"/>
					<m-button color="danger" @click="doDelete"><i-mdi-delete/> {{ $t('delete') }}</m-button>
				</m-flex>
			</m-alert>
			<a-captcha v-if="captcha" v-model="captchaToken"/>
		</m-flex>
	</m-form>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { Game } from '~/types/models';
import type { EventHook } from '@vueuse/core';
import clone from 'rfdc/default';

const router = useRouter();
const yesNoModal = useYesNoModal();
const showError = useQuickErrorToast();

const captchaToken = ref<string>('-');

const { t } = useI18n();

const props = withDefaults(defineProps<{
	url: string;
	createUrl?: string;
	redirectTo?: string;
	deleteRedirectTo?: string;
	mergeParams?: object;
	canSave?: boolean;
	flushChanges?: EventHook;
	deleteButton?: boolean;
	game?: Game;
	captcha?: boolean;
}>(), { deleteButton: true });

const emit = defineEmits(['submit']);
const vm = defineModel<any>();
const fc = computed(() => props.flushChanges ?? createEventHook());
const createUrl = computed(() => props.createUrl ?? getGameResourceUrl(props.url, props.game));

async function submit() {
	try {
		let params;
		if (props.mergeParams) {
			params = serializeObject({
				...clone(vm.value),
				...props.mergeParams,
				'h-captcha-response': captchaToken.value
			});
		} else {
			params = clone(vm.value);
			params ??= {};
			params['h-captcha-response'] = captchaToken.value;
		}

		if (!vm.value.id) {
			const model = await postRequest<{ id: number }>(createUrl.value, params);
			if (props.redirectTo) {
				router.replace(`${props.redirectTo}/${model.id}`);
			}
		} else {
			fc.value.trigger(await patchRequest(`${props.url}/${vm.value.id}`, params));
		}

		emit('submit');
	} catch (error) {
		showError(error);
	}
	captchaToken.value = '';
}

async function doDelete() {
	yesNoModal({
		title: t('are_you_sure'),
		desc: t('irreversible_action'),
		async yes() {
			await deleteRequest(`${props.url}/${vm.value.id}`);
			const to = props.deleteRedirectTo ?? props.redirectTo;
			if (to) {
				router.replace(to);
			}
		}
	});
}
</script>
