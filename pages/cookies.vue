<template>
    <page-block class="items-center">
        <h1>Cookies</h1>
        <content-block>
            {{$t('cookies_desc')}}
            <h2>{{$t('first_party_cookies')}}</h2>
            <a-table class="cookies-table">
                <thead>
                    <th>{{$t('name')}}</th>
                    <th>{{$t('cookie_explanation')}}</th>
                </thead>
                <tbody>
                    <tr>
                        <td>locale</td>
                        <td>{{$t('locale_cookie_desc')}}</td>
                    </tr>
                    <tr>
                        <td>color-scheme</td>
                        <td>{{$t('color_scheme_cookie_desc')}}</td>
                    </tr>
                    <tr>
                        <td>mods-displaymode</td>
                        <td>{{$t('displaymode_cookie_desc')}}</td>
                    </tr>
                    <tr>
                        <td>theme</td>
                        <td>{{$t('theme_cookie_desc')}}</td>
                    </tr>
                    <tr>
                        <td>allow-cookies</td>
                        <td>{{$t('allow_cookies_cookie_desc')}}</td>
                    </tr>
                    <tr>
                        <td>hidden-announcements</td>
                        <td>{{$t('hidden_announcements_cookie_desc')}}</td>
                    </tr>
                    <tr>
                        <td>{{$t('login_cookies')}}</td>
                        <td>{{$t('login_cookies_desc')}}</td>
                    </tr>
                </tbody>
            </a-table>
            <a-alert v-if="allowCookies === true" color="success">{{$t('cookies_allowed_desc')}}</a-alert>
            <a-alert v-else-if="allowCookies === false" color="danger">{{$t('cookies_denied_desc')}}</a-alert>
            <span v-else>{{$t('cookies_no_choice_desc')}}</span>
            <flex>
                <a-button v-if="allowCookies !== true" @click="allowCookies = true">{{$t('allow_cookies')}}</a-button>
                <a-button v-if="allowCookies !== false" color="danger" @click="disallowCookies">{{$t('disallow_cookies')}}</a-button>
            </flex>
            <h2>{{$t('third_party_cookies')}}</h2>
            {{$t('third_party_cookies_desc')}}
            <NuxtLink>Some random ad provider's policy</NuxtLink>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { clearAllCookies, longExpiration } from '~~/utils/helpers';

const allowCookies = useCookie<boolean>('allow-cookies', { expires: longExpiration() });

function disallowCookies() {
    clearAllCookies();
    allowCookies.value = false;
}
</script>

<style>
.cookies-table td {
    white-space: pre-wrap;
}
</style>