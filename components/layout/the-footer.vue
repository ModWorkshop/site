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
                ModWorkshop v3.0 Beta
                <span>
                    <i18n-t keypath="made_with_love">
                        <template #luffy>
                            <NuxtLink to="/user/Luffy">Luffy</NuxtLink>
                        </template>
                    </i18n-t>
                </span>
            </flex>
            <flex class="items-center">
                <i18n-t keypath="operated_by">
                    <template #company>
                        <a-img class="inline-block" src="milk_deluxe.webp" is-asset width="40" height="24"/> Milk Deluxe
                    </template>
                </i18n-t>
            </flex>
        </flex>
        <flex class="lg:ml-auto mb-auto items-center">
            <a-select v-model="store.colorScheme" style="width: 250px;" :options="colors">
                <template #any-option="{option}">
                    <div class="circle" :style="{backgroundColor: `var(--mws-${option.id})`, marginTop: '2px'}"/> {{option.name}}
                </template>
            </a-select>
            <a-select v-model="$i18n.locale" default="en-US" :options="locales">
                <template #any-option="{option}">
                    <a-img :src="getLocaleImg(option)" is-asset width="30" height="16"/> {{langNames[option]}}
                </template>
            </a-select>
        </flex>
    </footer>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { colorSchemes, longExpiration } from '~~/utils/helpers';

const unlockedOwO = useState('unlockedOwO');
const i18n = useI18n();
const store = useStore();

const savedColorScheme = useConsentedCookie('color-scheme', { expires: longExpiration() });
const savedLocale = useConsentedCookie('locale', { expires: longExpiration() });

function getLocaleImg(option: string) {
    return `locales/${option}.${option === 'owo' ? 'webp' : 'svg'}`;
}

const locales = computed(() => i18n.availableLocales.filter(option => i18n.locale.value == 'owo' || option != 'owo' || unlockedOwO.value));
const langNames = {
    'en-US': "English",
    'owo': 'OwO',
    'de-DE': "Deutsch"
};

watch(i18n.locale, val => savedLocale.value = val);

watch(() => store.colorScheme, val => {
    savedColorScheme.value = val;
});

const colors: { id: string, name: string }[] = [];

for (const key of colorSchemes) {
    colors.push({
        id: key,
        name: i18n.t(`color_${key}`)
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