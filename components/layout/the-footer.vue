<template>
    <footer>
        <flex gap="3">
            <a-link-button @click="scrollToTop">{{$t('return_to_top')}}</a-link-button>
            <a-link-button to="/docs/rules">{{$t('rules')}}</a-link-button>
            <a-link-button to="/docs/about">{{$t('about')}}</a-link-button>
            <a-link-button to="/docs/terms">{{$t('terms')}}</a-link-button>
            <a-link-button to="/docs/policy">{{$t('privacy')}}</a-link-button>
        </flex>
        <flex class="mt-5" column>
            ModWorkshop v3.0 Alpha
            <span>
                Made with ❤ by <NuxtLink to="/@Luffy">Luffy</NuxtLink>
            </span>
        </flex>
    </footer>
</template>

<script setup lang="ts">
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
    flex-direction: column;
    background-color: var(--header-footer-color);
    grid-area: footer;
}
</style>