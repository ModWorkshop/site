<template>
	<div>
		<Html :class="store.theme === 'light' ? 'light' : 'dark'"/>
		<NuxtLayout>
			<NuxtPage/>
			<NuxtLoadingIndicator/>
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
import { DateTime, Settings } from 'luxon';
import { useI18n } from 'vue-i18n';
import { useStore } from './store';

const store = useStore();
const yesNoModals = useState('yesNoModals', () => []);
const firstModal = computed(() => yesNoModals.value[yesNoModals.value.length-1]);
const { locale } = useI18n();
const savedLocale = useCookie('locale');

useHead({
	titleTemplate: (titleChunk) => {
		return titleChunk ? `${titleChunk} - ModWorkshop` : 'ModWorkshop';
    },
	bodyAttrs: {
		style: computed(() => `
			--primary-color: var(--mws-${store.colorScheme});
			--primary-hover-color: var(--mws-${store.colorScheme}-hover); 
			--primary-color-text: var(--mws-${store.colorScheme}-text)` 
		)
	},
	title: undefined,
});

locale.value = savedLocale.value;
Settings.defaultLocale = savedLocale.value;
</script>

<style>
.markdown h1, .markdown h2 {
	border-bottom: 1px solid #424242;
	padding-bottom: .3em;
}

h1, h2, h3, h4, h5, h6 {
	margin-top: 0;
	margin-bottom: .5rem;
}

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