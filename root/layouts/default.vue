<template>
    <div class="layout">
        <the-header/>
        <main>
            <a-toast v-if="user && (user.pending_email || !user.activated)" class="mt-2" color="warning" :closable="false">
                <span class="whitespace-pre">
                    {{ $t(user.pending_email ? 'pending_email' : 'inactive_account', [user.pending_email]) }}
                </span>
                <flex class="mr-auto">
                    <a-button :loading="resending" @click="resendVerification">{{$t('resend')}}</a-button>
                    <a-button v-if="user.pending_email" color="danger" :loading="resending" @click="cancelPending">{{$t('cancel')}}</a-button>
                </flex>
            </a-toast>
            
            <flex id="toaster" column gap="2">
				<TransitionGroup name="toasts">
					<a-toast v-for="toast of toasts" :key="toast.key" :title="toast.title" :desc="toast.desc" :color="toast.color"/>
				</TransitionGroup>
			</flex>

            <ClientOnly>
                <div id="div-gpt-ad-mws-1" class="ad mx-auto" style="max-height: 90px;max-height: 200px;text-align: center;position: relative;"/>
                <div ref="leftAd" :class="adClasses" style="left:0.5rem;">
                    <div id="div-gpt-ad-mws-2"/>
                </div>
                <div :class="adClasses" style="right:0.5rem;">
                    <div id="div-gpt-ad-mws-3"/>
                </div>
            </ClientOnly>

            <slot/>
            <flex v-if="store.activity" gap="2" class="text-xl ml-2 mr-auto mt-auto">
                <span :title="$t('users')"><a-icon icon="mdi:account"/> {{ store.activity.users }}</span>
                <span :title="$t('guests')"><a-icon icon="mdi:hand-wave"/> {{ store.activity.guests }}</span>
            </flex>
        </main>
        <flex v-if="allowCookies === undefined" class="cookie-banner">
            <a-alert color="warning" :icon="false" class="mt-auto mx-auto" style="z-index: 999" :title="$t('cookies_banner')">
                {{$t('cookies_banner_desc')}}
                <flex>
                    <a-button @click="allowCookies = true">{{$t('allow_cookies')}}</a-button>
                    <a-button color="danger" @click="allowCookies = false">{{$t('disallow_cookies')}}</a-button>
                    <a-button to="/cookies">{{$t('cookie_policy')}}</a-button>
                </flex>
            </a-alert>
        </flex>
        <the-footer/>
    </div>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Toast } from '~~/types/toast';
import { longExpiration } from '~~/utils/helpers';
import { storeToRefs } from 'pinia';

const toasts = useState<Toast[]>('toasts', () => []);

const allowCookies = useCookie<boolean>('allow-cookies', { expires: longExpiration() });

const store = useStore();
const { user } = storeToRefs(store);

const resending = ref(false);

async function resendVerification() {
    resending.value = true;
    await postRequest('email/resend');
    resending.value = false;
}

async function cancelPending() {
    resending.value = true;
    await postRequest('email/cancel-pending');
    await store.reloadUser();
    resending.value = false;
}

const adScroll = ref(false);
const leftAd = ref<HTMLDivElement>();
const adClasses = computed(() => ({
    'ad': true,
    'ad-sides': true,
    'ad-scroll': adScroll
}));

onMounted(() => {
    let def = 0;
    document.addEventListener("scroll", () => {
        if (leftAd.value) {
            if (!adScroll.value) {
                def = Math.max(def, leftAd.value.offsetTop);
            }

            adScroll.value = (window.scrollY - def) > -64;
        }
    });
});

useHead({
    script: [
        { 
            innerHTML: `
                !function(e){var s=new XMLHttpRequest;s.open("GET","https://api.enthusiastgaming.net/scripts/cdn.enthusiast.gg/script/eg-aps/production/eg-aps-bootstrap.bundle.js?site=modworkshop.net",!0),s.onreadystatechange=function(){var t;4==s.readyState&&(200<=s.status&&s.status<300||304==s.status)&&((t=e.createElement("script")).type="text/javascript",t.text=s.responseText,e.head.appendChild(t))},s.send(null)}((window,document));
                (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','GTM-M3R4JTX');
            `
        }
    ]
});
</script>

<style>
.cookie-banner {
    width: 100%;
    bottom: 0;
    padding: 1rem;
    position: fixed;
}
.layout {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: auto 1fr auto;
    grid-template-areas: 
        'header'
        'main'
        'footer';
    position: relative;
    z-index: 10;
}

main {
    grid-area: main;
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-x: hidden;
}
</style>