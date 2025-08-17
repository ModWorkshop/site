// Mostly meant as a bandaid fix for https://github.com/nuxt/nuxt/issues/13350
// See useRouteQuery composable
export default defineNuxtPlugin((nuxtApp) => {
    const isLoading = useState('loading', () => false);
    nuxtApp.hook('page:start', () => {
        isLoading.value = true;
    });

    nuxtApp.hook('page:finish', () => {
        isLoading.value = false;
    });
});
