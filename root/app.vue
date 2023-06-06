<template>
	<div>
		<NuxtLayout>
			<NuxtPage/>
			<NuxtLoadingIndicator color="var(--primary-color)"/>
		</NuxtLayout>
		<a-modal-form 
			v-if="firstModal"
			v-model="firstModal.modelValue"
			:title="firstModal.title || 'Are you sure?'"
			:desc="firstModal.desc"
			:desc-type="firstModal.descType"
			save-text="Yes"
			cancel-text="No" 
			size="sm" 
			@submit="firstModal.yes"
			@cancel="firstModal.no"
		/>
	</div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { YesNoModal } from './composables/useYesNoModal';
import { useStore } from './store';

const store = useStore();
const yesNoModals = useState<YesNoModal[]>('yesNoModals', () => []);
const firstModal = computed(() => yesNoModals.value[yesNoModals.value.length-1]);
const { t } = useI18n();
const { public: config } = useRuntimeConfig();

useHeadSafe({
	titleTemplate: (titleChunk) => {
		return titleChunk ? `${titleChunk} - ModWorkshop` : 'ModWorkshop';
    },
	htmlAttrs: {
		class: computed(() => `${store.theme === 'light' ? 'light' : 'dark'} ${store.colorScheme}-scheme`)
	},
	title: undefined,
});

useServerSeoMeta({
	ogTitle: 'ModWorkshop',
	description: t('mws_short_about'),
	ogDescription: t('mws_short_about'),
	ogImage: `${config.siteUrl}/assets/mws_logo_white.png`,
	themeColor: '#006ce0',
	twitterCard: 'summary',
});
</script>

<style>

/* p {
    margin-top: 0;
    margin-bottom: 1rem;
} */

dl, ol, ul {
  margin-top: 0;
  margin-bottom: 1rem;
}

#toaster {
    position: fixed;
    width: 300px;
	z-index: 9999;
    right: 8px;
}

.toasts-move, /* apply transition to moving elements */
.toasts-enter-active,
.toasts-leave-active {
  transition: all 0.25s ease;
}

.toasts-enter-from,
.toasts-leave-to {
  opacity: 0;
  transform: translateX(300px);
}
</style>