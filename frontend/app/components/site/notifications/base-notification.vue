<template>
	<a-user v-if="type == 'user' && object && typeof object == 'object'" :user="(object as User)" :avatar="false" @click.prevent/>
	<template v-else>
		<NuxtLink v-if="link" :to="link" @click.stop="clickLink">{{ name }}</NuxtLink>
		<span v-else>{{ name }}</span><span v-if="typeHint"> ({{ typeHint }})</span>
	</template>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { User } from '~/types/models';

const { notifType = 'err', object, ok } = defineProps<{
	notifType?: string;
	object: any;
	ok?: () => void;
}>();

const { t } = useI18n();
const { ctrl } = useMagicKeys();

const name = computed(() => {
	if (!object) {
		return null;
	}

	if (typeof object === 'string') {
		return object;
	}

	return object.name || t(notifType).toLowerCase();
});

const typeHint = computed(() => {
	const n = name.value;

	if (typeof object === 'string') {
		return null;
	}

	const type = t(notifType).toLowerCase();
	if (n !== type) {
		return type;
	}
});

const link = computed(() => typeof object === 'object' ? getObjectLink(notifType, object) : null);

function clickLink() {
	if (!ctrl?.value) {
		ok?.();
	}
}
</script>
