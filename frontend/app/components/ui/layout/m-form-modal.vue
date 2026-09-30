<template>
	<m-modal v-model="vModel" :size="size" :title="title">
		<template #title>
			<h2 v-if="title">{{ title }}</h2>
			<i-mdi-close class="cursor-pointer ml-auto text-xl" @click="onCancel"/>
		</template>
		<m-form class="flex overflow-hidden" @submit="onSubmit()">
			<m-flex column gap="4" class="overflow-hidden">
				<m-alert v-if="descType" :color="descType" :desc="desc"/>
				<span v-else-if="desc">{{ desc }}</span>
				<m-flex column gap="4" class="overflow-y-auto h-full p-2">
					<slot/>
				</m-flex>
				<m-flex class="w-1/3 ml-auto" gap="2">
					<m-button :disabled="!canSubmit || disableButtons" type="submit" class="flex-1">{{ saveText ?? $t('submit') }}</m-button>
					<m-button :disabled="disableButtons" color="secondary" class="flex-1" @click="onCancel">{{ cancelText ?? $t('cancel') }}</m-button>
				</m-flex>
			</m-flex>
		</m-form>
	</m-modal>
</template>

<script setup lang="ts">
const { canSubmit = true } = defineProps<{
	title?: string;
	desc?: string;
	descType?: string;
	size?: 'lg' | 'md' | 'sm';
	saveText?: string;
	cancelText?: string;
	canSubmit?: boolean;
}>();

const emit = defineEmits(['submit', 'cancel']);
const vModel = defineModel<boolean>({ required: true });
const showToast = useQuickErrorToast();
const disableButtons = ref(false);

watch(vModel, () => {
	if (vModel.value) {
		disableButtons.value = false;
	}
});

function onSubmit() {
	disableButtons.value = true;
	emit('submit', e => {
		showToast(e);
	});
	setTimeout(() => disableButtons.value = false, 3000);
}

function onCancel() {
	emit('cancel', e => {
		showToast(e);
	});
	vModel.value = false;
}
</script>
