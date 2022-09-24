import Popper from 'vue3-popper/dist/popper.esm';

export default defineNuxtPlugin(nuxtApp => {
    nuxtApp.vueApp.component('Popper', Popper);
});
