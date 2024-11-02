<template>
    <div class="layout">
        <the-header/>
        <main>
            <m-toast v-if="user && (user.pending_email || !user.activated)" class="mt-2" color="warning" :closable="false">
                <span class="whitespace-pre">
                    {{ $t(user.pending_email ? 'pending_email' : 'inactive_account', [user.pending_email]) }}
                </span>
                <m-flex class="mr-auto">
                    <m-button :loading="resending" @click="resendVerification">{{$t('resend')}}</m-button>
                    <m-button v-if="user.pending_email" color="danger" :loading="resending" @click="cancelPending">{{$t('cancel')}}</m-button>
                </m-flex>
            </m-toast>
            
            <m-flex id="toaster" column gap="2">
                <ClientOnly>
                    <TransitionGroup name="toasts">
                        <m-toast v-for="toast of toasts" :key="toast.key" :title="toast.title" :desc="toast.desc" :color="toast.color"/>
                    </TransitionGroup>
                </ClientOnly>
			</m-flex>

            <div id="mws-ads-top" class="ad mx-auto"/>
            <div id="mws-ads-top-mobile" class="ad mx-auto"/>
            
            <div ref="leftAd" :class="adClasses" style="left:0.5rem;">
                <div id="mws-ads-left"/>
            </div>
            <div :class="adClasses" style="right:0.5rem;">
                <div id="mws-ads-right"/>
            </div>

            <slot/>
            
            <div id="mws-ads-footer" class="ad mx-auto"/>
            <div id="mws-ads-footer-mobile" class="ad mx-auto"/>

            <div class="page-block-nm">
                <m-flex v-if="activity" gap="2" class="text-xl ml-2 mr-auto mt-auto">
                    <span :title="$t('users')"><i-mdi-account/> {{ activity.users }}</span>
                    <span :title="$t('guests')"><i-mdi-hand-wave/> {{ activity.guests }}</span>
                </m-flex>
            </div>
        </main>
        <m-flex v-if="allowCookies === undefined" class="cookie-banner">
            <m-alert color="warning" :icon="false" class="mt-auto mx-auto" style="z-index: 999" :title="$t('cookies_banner')">
                {{$t('cookies_banner_desc')}}
                <m-flex>
                    <m-button @click="allowCookies = true">{{$t('allow_cookies')}}</m-button>
                    <m-button color="danger" @click="allowCookies = false">{{$t('disallow_cookies')}}</m-button>
                    <m-button to="/cookies">{{$t('cookie_policy')}}</m-button>
                </m-flex>
            </m-alert>
        </m-flex>
        <the-footer/>
    </div>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import type { Toast } from '~~/types/toast';
import { longExpiration } from '~~/utils/helpers';
import { storeToRefs } from 'pinia';

const toasts = useState<Toast[]>('toasts', () => []);

const allowCookies = useCookie<boolean>('allow-cookies', { expires: longExpiration() });

const { ads, activity, reloadUser } = useStore();
const { user } = storeToRefs(useStore());

const resending = ref(false);

async function resendVerification() {
    resending.value = true;
    await postRequest('email/resend');
    resending.value = false;
}

async function cancelPending() {
    resending.value = true;
    await postRequest('email/cancel-pending');
    await reloadUser();
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
    if (!import.meta.client) {
        return;
    }

    if (user.value?.has_supporter_perks) {
        console.log("Detected supporter, enjoy no ads!");
        return;
    }

    let def = 0;
    document.addEventListener("scroll", () => {
        if (leftAd.value) {
            if (!adScroll.value) {
                def = Math.max(def, leftAd.value.offsetTop);
            }

            adScroll.value = (window.scrollY - def) > -64;
        }
    });
    
    console.log("Mount ads");

    const nitroAds = window['nitroAds'];

    const adConfig = {
        "refreshLimit": 0,
        "refreshTime": 30,
        "renderVisibleOnly": false,
        "refreshVisibleOnly": true,
        "report": {
            "enabled": true,
            "icon": true,
            "wording": "Report Ad",
            "position": "bottom-right-side"
        },
    };

    ads.push(nitroAds.createAd('mws-ads-left', {
        ...adConfig,
        sizes: [[ "160", "600" ]],
        report: {
            ...adConfig.report,
            position: 'bottom-left'
        }
    }));

    ads.push(nitroAds.createAd('mws-ads-right', {
        ...adConfig,
        sizes: [[ "160", "600" ]],
        report: {
            ...adConfig.report,
            position: 'bottom-right'
        }
    }));

    ads.push(nitroAds.createAd('mws-ads-top', {
        ...adConfig,
        sizes: [[ "970", "90" ], [ "728", "90" ]],
        mediaQuery: "(min-width: 1025px)"
    }));

    ads.push(nitroAds.createAd('mws-ads-top-mobile', {
        ...adConfig,
        sizes: [
            [ "320", "100" ], [ "320","50" ]
        ],
        mediaQuery: "(min-width: 768px) and (max-width: 1024px), (min-width: 320px) and (max-width: 767px)"
    }));

    ads.push(nitroAds.createAd('mws-ads-footer', {
        ...adConfig,
        sizes: [[ "728", "90" ]],
        mediaQuery: "(min-width: 1025px)"
    }));

    ads.push(nitroAds.createAd('mws-ads-footer-mobile', {
        ...adConfig,
        sizes: [
            [ "336", "280" ]
        ],
        mediaQuery: "(min-width: 768px) and (max-width: 1024px), (min-width: 320px) and (max-width: 767px)"
    }));
});

//EG Ads
// useHead({
//     script: [
//         { 
//             innerHTML: `
//                 !function(e){var s=new XMLHttpRequest;s.open("GET","https://api.enthusiastgaming.net/scripts/cdn.enthusiast.gg/script/eg-aps/production/eg-aps-bootstrap.bundle.js?site=modworkshop.net",!0),s.onreadystatechange=function(){var t;4==s.readyState&&(200<=s.status&&s.status<300||304==s.status)&&((t=e.createElement("script")).type="text/javascript",t.text=s.responseText,e.head.appendChild(t))},s.send(null)}((window,document));
//                 (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
//                 new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
//                 j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
//                 'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
//                 })(window,document,'script','dataLayer','GTM-M3R4JTX');
//             `
//         }
//     ]
// });
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

#mws-ads-top[data-request-id], #mws-ads-top-mobile[data-request-id] {
    margin: 1rem 0;
}

#mws-ads-footer[data-request-id], #mws-ads-footer-mobile[data-request-id] {
    margin: 3rem 0;
}
</style>