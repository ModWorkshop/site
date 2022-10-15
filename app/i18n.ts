import { createI18n } from "vue-i18n";
import en from '../locales/en';
import owo from '../locales/owo';

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
        'de-DE': {},
        'owo': owo
    }
});