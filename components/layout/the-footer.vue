<template>
    <footer>
        <flex column gap="3">
            <flex gap="3">
                <a-link-button @click="scrollToTop">{{$t('return_to_top')}}</a-link-button>
                <a-link-button to="/docs/rules">{{$t('rules')}}</a-link-button>
                <a-link-button to="/docs/about">{{$t('about')}}</a-link-button>
                <a-link-button to="/docs/terms">{{$t('terms')}}</a-link-button>
                <a-link-button to="/docs/policy">{{$t('privacy')}}</a-link-button>
            </flex>
            <flex column>
                ModWorkshop v3.0 Alpha
                <span>
                    Made with ❤ by <NuxtLink to="/user/Luffy">Luffy</NuxtLink>
                </span>
            </flex>
        </flex>
        <flex class="ml-auto">
            <div class="ml-auto">
                <a-select v-model="store.colorScheme" style="width: 200px;" :options="colors"/>
            </div>
        </flex>
    </footer>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';

const { t } = useI18n();
const store = useStore();
const savedColorScheme = useCookie('color-scheme');

watch(() => store.colorScheme, val => {
    savedColorScheme.value = val;
});

const colors = [];

for (const key of colorSchemes) {
    colors.push({
        id: key,
        name: t(`color_${key}`)
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
    padding: 1.5rem 2rem;
    display: flex;
    background-color: var(--header-footer-color);
    grid-area: footer;
}
</style>