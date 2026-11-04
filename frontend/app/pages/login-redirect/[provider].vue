<template>
	<page-block>
		<two-factor-login v-if="twoFactor"/>
		<m-flex v-else-if="!error" column class="mx-auto">
			<h2 class="mx-auto">{{ $t('logging_you_in') }}</h2>
			<m-loading/>
		</m-flex>
	</page-block>
</template>
<script setup lang="ts">
import { useStore } from '~/store';

definePageMeta({
	middleware: 'guests-only'
});

const { attemptLoginUser } = useStore();
const route = useRoute();
const error = ref();
const handleError = useHandleError();
const twoFactor = ref(false);

if (route.query.error) {
	showError({ statusCode: 500, statusMessage: route.query.error_description as string });
}

if (import.meta.client) {
	const newQuery = {};

	// For some reason we receive it as openid. but Laravel socialite expects it as openid_
	for (const [key, value] of Object.entries(route.query)) {
		newQuery[key.replace('openid.', 'openid_')] = value;
	}

	try {
		const data = await postRequest<{ two_factor?: boolean }>(`/social-logins/${route.params.provider}/login-callback`, serializeObject(newQuery));
		if (data.two_factor) {
			twoFactor.value = true;
		} else {
			await reloadToken();
			attemptLoginUser('/');
		}
	} catch (e) {
		handleError(e);
	}
}
</script>
