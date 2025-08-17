<template>
	<div>
		<NuxtLayout>
			<NuxtPage/>
			<NuxtLoadingIndicator color="var(--primary-color)"/>
		</NuxtLayout>
		<m-form-modal 
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
import "~/assets/css/normalize.css";
import "~/assets/css/base.css";
import "~/assets/css/markdown.css";
import "~/assets/css/github-dark.css";
import { useI18n } from 'vue-i18n';
import { useStore } from './store';

const store = useStore();
const yesNoModals = useState<YesNoModal[]>('yesNoModals', () => []);
const firstModal = computed(() => yesNoModals.value[yesNoModals.value.length-1]);
const { t, locale } = useI18n();
const { public: config } = useRuntimeConfig();

useHeadSafe({
	// titleTemplate: (titleChunk) => {
	// 	return titleChunk ? `${titleChunk} - ModWorkshop` : 'ModWorkshop';
    // },
	htmlAttrs: {
		class: computed(() => `${store.theme} ${store.colorScheme}-scheme`),
		lang: locale.value ?? 'en'
	},
	title: undefined,
});

if (import.meta.server) {
	useSeoMeta({
		ogTitle: 'ModWorkshop',
		description: t('mws_short_about'),
		ogDescription: t('mws_short_about'),
		ogImage: `${config.siteUrl}/assets/mws_logo_white.png`,
		themeColor: '#006ce0',
		twitterCard: 'summary',
	});
}
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
    width: 320px;
	z-index: 9999;
	margin-top: 8px;
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