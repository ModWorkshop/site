import { createI18n } from "vue-i18n";
import en from '../locales/en'

export default defineNuxtPlugin(nuxtApp => {
    const i18n = createI18n({
        globalInjection: true,
        messages: {
            en
        }
    });
    nuxtApp.vueApp.use(i18n);
});
