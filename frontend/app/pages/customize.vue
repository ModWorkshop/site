<template>
	<page-block size="2xs">
		<Title>{{ $t('customize') }}</Title>
		<m-flex class="content-block p-6" column gap="2">
			<m-alert v-if="!allowCookies" color="warning">
				{{ $t('customize_cookie_alert') }}
				<m-button class="mr-auto" to="cookies">{{ $t('cookie_policy') }}</m-button>
			</m-alert>
			<h2>{{ $t('site_wide') }}</h2>
			<m-flex class="p-4" column gap="4">
				<m-input :label="$t('theme')">
					<m-toggle-group v-model:selected="theme" class="text-xl" gap="1" button-style="button">
						<m-toggle-group-item value="system" class="flex-1">
							<i-mdi-devices/> {{ $t('system_theme') }}
						</m-toggle-group-item>
						<m-toggle-group-item value="dark" class="flex-1">
							<i-mdi-weather-night/> {{ $t('dark_theme') }}
						</m-toggle-group-item>
						<m-toggle-group-item value="light" class="flex-1">
							<i-mdi-white-balance-sunny/> {{ $t('light_theme') }}
						</m-toggle-group-item>
					</m-toggle-group>
				</m-input>
				<m-flex>
					<m-select v-model="store.colorScheme" :options="colors" :label="$t('color')">
						<template #any-option="{ option }">
							<div class="circle" :style="{ backgroundColor: `var(--mws-${option.id})` }"/> {{ $t(`color_${option.id}`) }}
						</template>
					</m-select>
				</m-flex>
				<m-select v-model="locale" default="en" :options="locales" :value-by="option => option.code" :label="$t('language')"/>
				<m-input v-model="useRelativeTime" type="checkbox" :label="$t('use_relative_time')" :desc="$t('use_relative_time_desc')"/>
				<m-input v-model="useSystemDateFormat" type="checkbox" :label="$t('use_system_date_format')" :desc="$t('use_system_date_format_desc')"/>
			</m-flex>
			<h2>Mods</h2>
			<m-flex class="p-4" column gap="4">
				<m-input :label="$t('display_mode')">
					<m-toggle-group v-model:selected="displayMode" class="text-2xl" gap="1" button-style="button">
						<m-toggle-group-item :value="0" class="flex-1">
							<i-mdi-view-grid/>
						</m-toggle-group-item>
						<m-toggle-group-item :value="1" class="flex-1">
							<i-mdi-view-list/>
						</m-toggle-group-item>
						<m-toggle-group-item :value="2" class="flex-1">
							<i-mdi-view-headline/>
						</m-toggle-group-item>
					</m-toggle-group>
				</m-input>
			</m-flex>
		</m-flex>
	</page-block>
</template>

<script setup lang="ts">
import { useStore } from '~/store';

const i18n = useI18n();
const store = useStore();
const savedColorScheme = useConsentedCookie('color-scheme', { expires: longExpiration() });
const savedLocale = useConsentedCookie<string>('locale', { expires: longExpiration() });
const displayMode = useConsentedCookie('mods-displaymode', { default: () => 0, expires: longExpiration() });
const useRelativeTime = useConsentedCookie('use-relative-time', { default: () => true, expires: longExpiration() });
const useSystemDateFormat = useConsentedCookie('use-system-date-format', { expires: longExpiration() });
const theme = ref<'dark' | 'light' | 'system'>(store.theme ?? 'dark');
const allowCookies = useCookie<boolean>('allow-cookies', { expires: longExpiration() });

const locale = ref(i18n.locale.value);

const colors: { id: string }[] = [];

for (const key of colorSchemes) {
	colors.push({
		id: key
	});
}

const locales = computed(() => i18n.locales.value);

watch(locale, val => {
	i18n.setLocale(val);
	savedLocale.value = val;
});

watch(() => store.colorScheme, val => {
	savedColorScheme.value = val;
});

watch(theme, val => store.setTheme(val));
</script>
