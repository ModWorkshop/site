<template>
	<page-block size="2xs">
		<Title>{{ $t('support_us') }}</Title>
		<m-flex gap="6" class="items-center" column>
			<m-img alt="logo" :src="logo" width="128" height="128" is-asset/>
			<m-flex class="items-center" column gap="3">
				<span class="h1 text-primary m-auto">{{ $t('support_mws') }}</span>
				<span class="h2 whitespace-pre text-center">{{ $t('supporter_desc') }}</span>
			</m-flex>

			<m-flex gap="3">
				<m-card v-for="pkg of supporterPackages?.data" :key="pkg.id" :title="pkg.name" :padding="6" :gap="3">
					<div><span class="h1">€{{ pkg.price }}</span><span class="text-secondary"> / {{ specificUnitDuration(pkg.duration_number, pkg.duration_type) }}</span> </div>
					<!-- <span class="text-base">
						<m-dropdown type="tooltip" dropdown-class="p-2" :tool-tip-delay="0.1" :disabled="!!user">
							<m-button
								size="lg"
								:disabled="!user"
								:loading="loading"
								@click="openPlan(pkg)"
							>
								Select Plan
							</m-button>

							<template #content>{{ $t('login_required') }}</template>
						</m-dropdown>
					</span> -->
				</m-card>
			</m-flex>

			<m-flex column class="items-center" gap="3">
				<!-- <span class="h2 text-center">{{ $t('supporter_just_donate') }}</span> -->
				<donation-button link="paypal.me/tsunavr"/>
				<m-alert color="warning" class="whitespace-pre">
					{{ $t('supporter_only_donations') }}
				</m-alert>
			</m-flex>

			<m-flex v-if="supporters?.data.length" column gap="2" class="items-center">
				<span class="h2">{{ $t('currently_supported') }}</span>
				<m-flex wrap class="mb-3" style="max-width: 500px;">
					<NuxtLink v-for="supporter of supporters.data" :key="supporter.id" :to="`user/${supporter.user!.unique_name ?? supporter.user!.id}`">
						<m-avatar :src="supporter.user!.avatar"/>
					</NuxtLink>
				</m-flex>
			</m-flex>

			<m-flex column gap="4">
				<span class="h2 text-center">{{ $t('supporter_you_get') }}</span>
				<m-flex wrap class="perks justify-center whitespace-pre-line" gap="3">
					<m-content-block>
						<i-mdi-advertisements-off class="text-5xl"/>
						<strong>{{ $t('supporter_no_ads') }}</strong>
					</m-content-block>
					<m-content-block>
						<i-mdi-harddisk class="text-5xl"/>
						<strong>
							{{ $t('supporter_extra_storage', {
								from: friendlySize(settings?.mod_storage_size ?? 0),
								to: friendlySize(settings?.supporter_mod_storage_size ?? 0)
							}) }}
						</strong>
					</m-content-block>
					<m-content-block>
						<i-mdi-image-size-select-actual class="text-5xl"/>
						<strong>{{ $t('supporter_profile_mod_background') }}</strong>
					</m-content-block>
					<m-content-block>
						<i-mdi-format-color-fill class="text-5xl"/>
						<strong>{{ $t('supporter_custom_name_color') }}</strong>
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
						}"
						/>
						<strong>{{ $t('supporter_supporter_tag') }}</strong>
					</m-content-block>
				</m-flex>
			</m-flex>

			<m-flex column gap="3">
				<span class="h2 text-center">{{ $t('supporter_faq') }}</span>
<!-- 
				<m-alert>
					<b>{{ $t('supporter_faq_q_1') }}</b>
					<i>{{ $t('supporter_faq_a_1') }}</i>
				</m-alert> -->
				<!-- <m-alert>
					<b>{{ $t('supporter_faq_q_2') }}</b>
					<i>{{ $t('supporter_faq_a_2') }}</i>
				</m-alert> -->
				<m-alert>
					<b>{{ $t('supporter_faq_q_3') }}</b>
					<i>{{ $t('supporter_faq_a_3') }}</i>
				</m-alert>
			</m-flex>
		</m-flex>
	</page-block>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import { type SupporterPackage, type Supporter } from '~/types/models';

const { settings } = useStore();
const store = useStore();
const showError = useQuickErrorToast();

const logo = computed(() => store.theme === 'light' ? 'mws_logo_black.svg' : 'mws_logo_white.svg');

const loading = ref(false);

const { data: supporters } = await useFetchMany<Supporter>('supporters?active_only=1&sort_by_id=1');
const { data: supporterPackages } = await useFetchMany<SupporterPackage>('supporter-packages');

async function openPlan(supporterPackage: SupporterPackage) {
	loading.value = true;

	try {
		// const { ident } = await postRequest<{ ident: string }>('supporters/tebex/baskets', { supporter_package_id: supporterPackage.id });
		// window['Tebex'].checkout.init({ ident });
		// window['Tebex'].checkout.launch();
	} catch (error) {
		showError(error);
	}

	loading.value = false;
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
	width: 250px;
	height: 250px;
	align-items: center;
	text-align: center;
	justify-content: center;
	font-size: 1.15rem;
}
</style>
