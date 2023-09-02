<template>
    <footer>
        <flex column gap="5">
            <flex wrap gap="3">
                <a-link-button @click="scrollToTop">{{$t('return_to_top')}}</a-link-button>
                <a-link-button to="/document/rules">{{$t('rules')}}</a-link-button>
                <a-link-button to="/document/about">{{$t('about')}}</a-link-button>
                <a-link-button to="/document/terms">{{$t('terms')}}</a-link-button>
                <a-link-button to="/document/policy">{{$t('privacy')}}</a-link-button>
                <a-link-button to="/cookies">{{$t('cookie_policy')}}</a-link-button>
            </flex>
            <flex column>
                {{ $t('mws_build_version', { version: buildVersion }) }}
                <span>
                    <i18n-t keypath="made_with_love" scope="global">
                        <template #luffy>
                            <NuxtLink to="/user/Luffy">Luffy</NuxtLink>
                        </template>
                    </i18n-t>
                </span>
            </flex>
            <flex class="items-center">
                <i18n-t keypath="operated_by" scope="global">
                    <template #company>
                        <a-img class="inline-block" src="milk_deluxe.webp" loading="lazy" is-asset width="40" height="24" alt="CompanyLogo"/> Milk Deluxe
                    </template>
                </i18n-t>
            </flex>
        </flex>
        <flex class="lg:ml-auto mb-auto items-center">
            <a-select v-model="store.colorScheme" style="width: 250px;" :options="colors">
                <template #any-option="{option}">
                    <div class="circle" :style="{backgroundColor: `var(--mws-${option.id})`, marginTop: '2px'}"/> {{$t(`color_${option.id}`)}}
                </template>
            </a-select>
            <a-select v-model="locale" default="en" :options="locales" :value-by="option => option.code"/>
        </flex>
    </footer>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { colorSchemes, longExpiration } from '~~/utils/helpers';
import { Settings } from 'luxon';

const { public: runtimeConfig } = useRuntimeConfig();

const unlockedOwO = useState('unlockedOwO');
const i18n = useI18n();
const store = useStore();

const savedColorScheme = useConsentedCookie('color-scheme', { expires: longExpiration() });
const savedLocale = useConsentedCookie('locale', { expires: longExpiration() });
const locale = ref(i18n.locale.value);

const locales = computed(() => i18n.locales.value.filter(option => i18n.locale.value == 'owo' || (typeof option == 'object' && option.code) != 'owo' || unlockedOwO.value));
const buildVersion = computed(() => (runtimeConfig.versionHash || 'N/A').substring(0, 7));

watch(locale, val => {
    i18n.setLocale(val);
    savedLocale.value = val;
    Settings.defaultLocale = val;
});

watch(() => store.colorScheme, val => {
    savedColorScheme.value = val;
});

const colors: { id: string }[] = [];

for (const key of colorSchemes) {
    colors.push({
        id: key
    });
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        left: 0,
        behavior: 'smooth'
    });
}
</script>

<style>
footer {
    max-width: 100vw;
    margin-top: 8px;
    padding: 1.5rem 2rem;
    gap: 24px;
    display: flex;
    background-color: var(--header-footer-color);
    grid-area: footer;
}

@media (max-width:768px) {
    footer {
        flex-direction: column-reverse;
    }
}
</style>