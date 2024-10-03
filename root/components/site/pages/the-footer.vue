<template>
    <footer>
        <m-flex class="page-block-nm">
            <m-flex column gap="5">
                <m-flex wrap gap="3">
                    <m-link @click="scrollToTop">{{$t('return_to_top')}}</m-link>
                    <m-link to="/document/rules">{{$t('rules')}}</m-link>
                    <m-link to="/about">{{$t('about')}}</m-link>
                    <m-link to="/document/terms">{{$t('terms')}}</m-link>
                    <m-link to="/document/impressum">{{$t('impressum')}}</m-link>
                    <m-link to="/document/policy">{{$t('privacy')}}</m-link>
                    <m-link to="/cookies">{{$t('cookie_policy')}}</m-link>
                    <m-link to="https://status.modworkshop.net">{{$t('status')}}</m-link>
                </m-flex>
                <m-flex column>
                    ModWorkshop {{ runtimeConfig.version }} ({{ commitHash }})
                    <span>
                        <i18n-t keypath="made_with_love" scope="global">
                            <template #luffy>
                                <NuxtLink to="/user/Luffy">Luffy</NuxtLink>
                            </template>
                        </i18n-t>
                    </span>
                </m-flex>
                <m-flex class="items-center">
                    <i18n-t keypath="operated_by" scope="global">
                        <template #company>
                            <m-img class="inline-block" src="milk_deluxe.webp" loading="lazy" is-asset width="40" height="24" alt="CompanyLogo"/> Milk Deluxe
                        </template>
                    </i18n-t>
                </m-flex>
            </m-flex>
            <m-flex class="lg:ml-auto mb-auto items-center">
                <m-select v-model="store.colorScheme" style="width: 250px;" :options="colors">
                    <template #any-option="{option}">
                        <div class="circle" :style="{backgroundColor: `var(--mws-${option.id})`, marginTop: '2px'}"/> {{$t(`color_${option.id}`)}}
                    </template>
                </m-select>
                <m-select v-model="locale" default="en" :options="locales" :value-by="option => option.code"/>
            </m-flex>
        </m-flex>
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
const savedLocale = useConsentedCookie<string>('locale', { expires: longExpiration() });
const locale = ref(i18n.locale.value);

const locales = computed(() => i18n.locales.value.filter(option => i18n.locale.value == 'owo' || (typeof option == 'object' && option.code) != 'owo' || unlockedOwO.value));
const commitHash = computed(() => (runtimeConfig.commitHash || 'N/A').substring(0, 7));

watch(locale, val => {
    i18n.setLocale(val);
    savedLocale.value = val;
    Settings.defaultLocale = val;
});

Settings.defaultLocale = savedLocale.value ?? 'en';

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
    margin-top: 2rem;
    padding-top: 2rem;
    padding-bottom: 2rem;
    gap: 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: var(--header-footer-color);
    grid-area: footer;
}

@media (max-width:768px) {
    footer {
        flex-direction: column-reverse;
    }
}
</style>