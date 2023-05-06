import { i18n } from "~~/app/i18n";

export default defineNuxtPlugin(nuxtApp => {
    nuxtApp.vueApp.use(i18n);
});
