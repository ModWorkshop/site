<template>
	<page-block class="items-center">
		<h1>{{ $t('cookies') }}</h1>
		<m-content-block>
			{{ $t('cookies_desc') }}
			<h2>{{ $t('first_party_cookies') }}</h2>
			<m-table class="cookies-table" alt-background>
				<template #head>
					<th>{{ $t('name') }}</th>
					<th>{{ $t('cookie_explanation') }}</th>
				</template>
				<template #body>
					<tr>
						<td>locale</td>
						<td>{{ $t('locale_cookie_desc') }}</td>
					</tr>
					<tr>
						<td>color-scheme</td>
						<td>{{ $t('color_scheme_cookie_desc') }}</td>
					</tr>
					<tr>
						<td>mods-displaymode</td>
						<td>{{ $t('displaymode_cookie_desc') }}</td>
					</tr>
					<tr>
						<td>theme</td>
						<td>{{ $t('theme_cookie_desc') }}</td>
					</tr>
					<tr>
						<td>allow-cookies</td>
						<td>{{ $t('allow_cookies_cookie_desc') }}</td>
					</tr>
					<tr>
						<td>hidden-announcements</td>
						<td>{{ $t('hidden_announcements_cookie_desc') }}</td>
					</tr>
					<tr>
						<td>{{ $t('login_cookies') }}</td>
						<td>{{ $t('login_cookies_desc') }}</td>
					</tr>
					<tr>
						<td>use-relative-time</td>
						<td>{{ $t('use_relative_time_desc') }}</td>
					</tr>
					<tr>
						<td>use-system-date-format</td>
						<td>{{ $t('use_system_date_format_desc') }}</td>
					</tr>
				</template>
			</m-table>
			<m-alert v-if="allowCookies == true" color="success">{{ $t('cookies_allowed_desc') }}</m-alert>
			<m-alert v-else-if="allowCookies == false" color="danger">{{ $t('cookies_denied_desc') }}</m-alert>
			<span v-else>{{ $t('cookies_no_choice_desc') }}</span>
			<m-flex>
				<m-button v-if="allowCookies != true" @click="allowCookies = true">{{ $t('allow_cookies') }}</m-button>
				<m-button v-if="allowCookies != false" color="danger" @click="disallowCookies">{{ $t('disallow_cookies') }}</m-button>
			</m-flex>
			<h2>{{ $t('third_party_cookies') }}</h2>
			{{ $t('third_party_cookies_desc') }}
			<!-- <NuxtLink to="https://www.enthusiastgaming.com/privacy-policy/">EnthusiastGaming Policy</NuxtLink> -->
			<NuxtLink to="https://nitropay.com/privacy/">Nitro Policy</NuxtLink>
		</m-content-block>
	</page-block>
</template>

<script setup lang="ts">
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
