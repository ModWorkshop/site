<template>
    <div class="layout">
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
            <slot/>
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
        <the-header/>
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
    await usePost('email/resend');
    resending.value = false;
}

async function cancelPending() {
    resending.value = true;
    await usePost('email/cancel-pending');
    await store.reloadUser();
    resending.value = false;
}
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
}

main {
    grid-area: main;
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-x: hidden;
    z-index: 10;
}
</style>