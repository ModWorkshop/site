<template>
    <footer class="content-block" ref="footerElement">
        <m-flex column class="p-4 footer-content" gap="8">
            <div class="footer-links">
                <m-flex column class="mb-auto footer-customize" gap="3">
                    <m-flex class="items-center">
                        <m-select v-model="store.colorScheme" style="width: 220px;" :options="colors">
                            <template #any-option="{option}">
                                <div class="circle" :style="{backgroundColor: `var(--mws-${option.id})`, marginTop: '2px'}"/> {{$t(`color_${option.id}`)}}
                            </template>
                        </m-select>
                        <m-select v-model="locale" default="en" :options="locales" :value-by="option => option.code"/>
                    </m-flex>
                    <m-toggle-group v-model:selected="theme" gap="1" button-style="button" class="mr-auto">
                        <m-toggle-group-item value="system" class="flex-1">
                            <i-mdi-devices/>
                        </m-toggle-group-item>
                        <m-toggle-group-item value="dark" class="flex-1">
                            <i-mdi-weather-night/>
                        </m-toggle-group-item>
                        <m-toggle-group-item value="light" class="flex-1">
                            <i-mdi-white-balance-sunny/>
                        </m-toggle-group-item>
                    </m-toggle-group>
                </m-flex>
                <m-flex column gap="4" style="grid-area: pages;">
                    <m-link @click="scrollToTop">{{$t('return_to_top')}}</m-link>
                    <m-link to="/support">{{$t('support_us')}}</m-link>
                    <m-link to="/about">{{$t('about')}}</m-link>
                    <m-link to="/document/rules">{{$t('rules')}}</m-link>
                </m-flex>
                <m-flex column gap="4" style="grid-area: sites;">
                    <m-link to="https://translate.modworkshop.net/">{{$t('translation_site')}}</m-link>
                    <m-link to="https://status.modworkshop.net">{{$t('status')}}</m-link>
                    <m-link to="https://wiki.modworkshop.net/">{{$t('wiki')}}</m-link>
                    <m-link to="https://api.modworkshop.net">{{$t('api')}}</m-link>
                </m-flex>
                <m-flex column gap="4" style="grid-area: legal;">
                    <m-link to="/document/terms">{{$t('terms')}}</m-link>
                    <m-link to="/cookies">{{$t('cookie_policy')}}</m-link>
                    <m-link to="/document/policy">{{$t('privacy')}}</m-link>
                    <m-link to="/document/impressum">{{$t('impressum')}}</m-link>
                </m-flex>
            </div>

            <m-flex gap="3" class="flex-1 items-center place-content-center" wrap>
                <m-flex column gap="2">
                    <span>
                        ModWorkshop {{ runtimeConfig.version }} (<NuxtLink :to="`https://github.com/ModWorkshop/site/commit/{commitHash}`">{{ commitHash }}</NuxtLink>) Made With ❤ By <NuxtLink to="/user/Luffy">Luffy</NuxtLink>
                    </span>
                    <m-flex class="items-center">
                        Operated By <m-img class="inline-block" src="milk_deluxe.webp" loading="lazy" is-asset width="40" height="24" alt="CompanyLogo"/> Milk Deluxe
                    </m-flex>
                </m-flex>
                <m-flex class="sm:ml-auto text-2xl" gap="3">
                    <m-link to="https://discord.gg/Eear4JW"><i-ri-discord-fill/></m-link>
                    <m-link to="https://x.com/ModWorkshop"><i-ri-twitter-x-fill/></m-link>
                    <m-link to="https://bsky.app/profile/modworkshop.bsky.social"><i-ri-bluesky-fill/></m-link>
                    <m-link to="https://www.youtube.com/@modworkshop-yt"><i-ri-youtube-fill/></m-link>
                    <m-link to="https://github.com/ModWorkshop"><i-ri-github-fill/></m-link>
                </m-flex>
            </m-flex>
        </m-flex>
    </footer>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
const { public: runtimeConfig } = useRuntimeConfig();

const i18n = useI18n();
const store = useStore();
const footerElement = ref();
const theme = ref<'dark'|'light'|'system'>(store.theme ?? 'dark');

const savedColorScheme = useConsentedCookie('color-scheme', { expires: longExpiration() });
const savedLocale = useConsentedCookie<string>('locale', { expires: longExpiration() });
const locale = ref(i18n.locale.value);

const locales = computed(() => i18n.locales.value);

const commitHash = computed(() => (runtimeConfig.commitHash || 'N/A').substring(0, 7));

watch(locale, val => {
    i18n.setLocale(val);
    savedLocale.value = val;
});

watch(() => store.colorScheme, val => savedColorScheme.value = val);
watch(theme, val => store.setTheme(val));

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
.footer-links {
    display: grid;
    gap: 1rem;
    grid-template-areas:
        "customize pages sites legal";
    grid-template-columns: minmax(0, 3fr) repeat(2, minmax(0, 1fr));
}

.footer-customize {
    grid-area: customize;
    margin-right: auto;
}

.footer-content {
    width: 75%;
}

@media (max-width:1280px) {
    .footer-content {
        width: 85%;
    }
}

@media (max-width:900px) {
    .footer-links {
        grid-template-areas:
            "customize customize customize"
            "pages sites legal";
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }
    
    .footer-customize {
        margin-left: auto;
    }

    .footer-content {
        width: 90%;
    }
}

footer {
    max-width: 100vw;
    margin-top: 1rem;
    padding-top: 1rem;
    padding-bottom: 1rem;
    gap: 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: var(--header-footer-color);
    grid-area: footer;
}
</style>