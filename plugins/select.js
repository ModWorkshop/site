import VueEasyLightbox from 'vue-easy-lightbox';
import VueObserveVisibility from 'vue3-observe-visibility2';

export default defineNuxtPlugin(nuxtApp => {
    // Register the component globally
    nuxtApp.vueApp.use(VueEasyLightbox);
    nuxtApp.vueApp.use(VueObserveVisibility);
});
