import VueEasyLightbox from 'vue-easy-lightbox';
import VueObserveVisibility from 'vue3-observe-visibility2';
import Popper from 'vue3-popper/dist/popper.esm';
import { UidPlugin } from '@shimyshack/uid'

export default defineNuxtPlugin(nuxtApp => {
    // Register the component globally
    nuxtApp.vueApp.component('Popper', Popper);
    nuxtApp.vueApp.use(VueEasyLightbox);
    nuxtApp.vueApp.use(VueObserveVisibility);
    nuxtApp.vueApp.use(UidPlugin);
});
