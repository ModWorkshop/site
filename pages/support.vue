<template>
    <page-block size="2xs">
        <Title>{{$t('support_us')}}</Title>
        <m-flex gap="6" class="items-center" column>
            <m-img alt="logo" :src="logo" width="128" height="128" is-asset/>
            <m-flex class="items-center" column>
                <span class="h1 text-primary m-auto">{{ $t('support_mws') }}</span>
                <span class="h2">{{$t('supporter_desc')}}</span>
            </m-flex>

            <m-flex column gap="4">
                <span class="h2 text-center">{{$t('supporter_you_get')}}</span>
                <m-flex wrap class="perks">
                    <m-content-block>
                        <i-mdi-advertisements-off class="text-5xl"/>
                        <strong>{{$t('supporter_no_ads')}}</strong>
                    </m-content-block>
                    <m-content-block>
                        <i-mdi-harddisk class="text-5xl"/>
                        <strong>
                            {{$t('supporter_extra_storage', { 
                                from: friendlySize(settings?.mod_storage_size ?? 0),
                                to: friendlySize(settings?.supporter_mod_storage_size ?? 0)
                            })}}
                        </strong>
                    </m-content-block>
                    <m-content-block>
                        <i-mdi-image-size-select-actual class="text-5xl"/>
                        <strong>{{$t('supporter_profile_mod_background')}}</strong>
                    </m-content-block>
                    <m-content-block>
                        <i-mdi-format-color-fill class="text-5xl"/>
                        <strong>{{$t('supporter_custom_name_color')}}</strong>
                    </m-content-block>
                    <m-content-block>
                        <a-user static :user="{
                            id: 0,
                            unique_name: '',
                            avatar: '',
                            custom_color: '#ff00f5',
                            color: '#ff00f5',
                            banner: '',
                            bio: '',
                            private_profile: false,
                            invisible: false,
                            custom_title: '',
                            donation_url: '',
                            email: '',
                            name: 'User',
                            active_supporter: { id: 0, user_id: 0, expired: false }, show_tag: 'supporter_or_role'
                        }"/>
                        <strong>{{$t('supporter_supporter_tag')}}</strong>
                    </m-content-block>
                </m-flex>
            </m-flex>

            <m-flex column class="items-center" gap="2">
                <m-dropdown type="tooltip" dropdown-class="p-2" :tool-tip-delay="0.1" :disabled="!!user">
                    <m-button 
                        size="lg"
                        :to="user ? `https://sponsor.nitrocnct.com/subscribe?token=${user.nitro_token}&cancelUrl=${supportUrl}&successUrl=${supportUrl}` : undefined"
                        :disabled="!user"
                    >
                        <i-mdi-heart-multiple/> {{ $t('supporter_via_nitro') }}
                    </m-button>
    
                    <template #content>{{$t('login_required')}}</template>
                </m-dropdown>
                <m-button v-if="!user" to="/login">{{$t('login')}}</m-button>
                <m-alert v-if="user?.active_supporter" color="success" :icon="false">
                    <i18n-t keypath="supporter_already" tag="div" class="whitespace-pre text-center" scope="global">
                        <template #time>
                            <m-time-ago null-is-never :time="user.active_supporter.expire_date"/>
                        </template>
                    </i18n-t>
                </m-alert>
            </m-flex>

            <m-flex column v-if="supporters?.data.length" gap="2" class="items-center">
                <span class="h2">{{$t('currently_supported')}}</span>
                <m-flex wrap class="mb-3" style="max-width: 500px;">
                    <NuxtLink v-for="supporter of supporters.data" :key="supporter.id" :to="`user/${supporter.user!.unique_name ?? supporter.user!.id}`">
                        <m-avatar :src="supporter.user!.avatar"/>
                    </NuxtLink>
                </m-flex>
            </m-flex>

            <m-flex column class="items-center" gap="3">
                <b>{{$t('supporter_just_support')}}</b>
                <donation-button link="paypal.me/tsunavr"/>
            </m-flex>

            <m-alert>
                <b>{{$t('supporter_faq_q_1')}}</b>
                <i>{{$t('supporter_faq_a_1')}}</i>
                <b>{{$t('supporter_faq_q_2')}}</b>
                <i>{{$t('supporter_faq_a_2')}}</i>
                <b>{{$t('supporter_faq_q_3')}}</b>
                <i>{{$t('supporter_faq_a_3')}}</i>
                <b>{{$t('supporter_faq_q_4')}}</b>
                <i>{{$t('supporter_faq_a_4')}}</i>
            </m-alert>
        </m-flex>
    </page-block>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import type { Supporter } from '~~/types/models';

const { public: config } = useRuntimeConfig();
const { user, settings } = useStore();
const store = useStore();
const logo = computed(() => store.theme === 'light' ? 'mws_logo_black.svg' : 'mws_logo_white.svg');
const supportUrl = `${config.siteUrl}/support`;

const { data: supporters } = await useFetchMany<Supporter>('supporters?active_only=1&sort_by_id=1');
if (user) {
    const { data: subData } = await useFetchData<Supporter>('supporters/nitro-check');
    user.active_supporter = subData.value ?? undefined;
}
</script>

<style scoped>
.no-bullets {
    text-align: center;
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.perks > div {
    width: 180px;
    height: 180px;
    align-items: center;
    text-align: center;
    justify-content: center;
}
</style>