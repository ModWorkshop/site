import VueEasyLightbox from 'vue-easy-lightbox';
import Popper from 'vue3-popper/dist/popper.esm';

export default defineNuxtPlugin(nuxtApp => {
    // Register the component globally
    nuxtApp.vueApp.component('Popper', Popper);
    nuxtApp.vueApp.use(VueEasyLightbox);
});
