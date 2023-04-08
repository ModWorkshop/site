import { createI18n } from "vue-i18n";
import en from '../locales/en';
import owo from '../locales/owo';
import ch from "~~/locales/ch";
import es from "~/locales/es";

export const i18n = createI18n({
    globalInjection: true,
    global: true,
    legacy: false,
    fallbackLocale: {
        default: ['en-US'],
        debug: []
    },
    messages: {
        'en-US': en,
        'zh-cn': ch,
        'es': es,
        'owo': owo
    }
});