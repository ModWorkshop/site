<template>
	<m-form v-model="userForm" float-save-gui autocomplete="off" :flush-changes="fc" @submit="save">
		<m-flex gap="6" column>
			<m-img-uploader
				v-model="userForm.avatar_file"
				clear-button
				:label="$t('avatar')"
				:desc="$t('user_avatar_desc', { size: imageSize })"
				:src="user.avatar"
				:max-file-size="settings?.image_max_file_size"
			>
				<template #image="{ src }">
					<m-avatar size="xl" :src="src"/>
					<m-avatar size="lg" :src="src"/>
					<m-avatar size="md" :src="src"/>
				</template>
			</m-img-uploader>
			<m-flex flex>
				<m-input v-model="userForm.name" :label="$t('display_name')" maxlength="30"/>
				<m-input v-model="userForm.custom_color" :disabled="!user.has_supporter_perks" :label="true" :desc="$t('custom_color_desc')" type="color">
					<template #label>
						{{ $t('custom_color') }} {{ $t('supporters_only') }}
					</template>
				</m-input>
			</m-flex>
			<md-editor v-model="userForm.bio" rows="12" :label="$t('bio')" :desc="$t('bio_desc')"/>

			<mod-select
				v-model="userForm.pinned_mod_ids"
				:fetch-params="{ user_id: user.id, including_collab: true }"
				:max="5"
				multiple
				:label="$t('pinned_mods')"
				:desc="$t('pinned_mods_desc')"
			/>

			<m-img-uploader
				v-model="userForm.banner_file"
				clear-button
				:label="$t('banner')" :desc="$t('user_banner_desc', { size: imageSize })"
				:src="user.banner"
				:max-file-size="settings?.image_max_file_size"
			>
				<template #image="{ src }">
					<m-banner :src="src" url-prefix="users/images"/>
				</template>
			</m-img-uploader>

			<m-img-uploader
				v-model="userForm.background_file"
				clear-button
				:label="true"
				:src="user.extra!.background"
				:max-file-size="settings?.image_max_file_size"
				:disabled="!user.has_supporter_perks"
			>
				<template #label>
					{{ $t('supporter_background') }} {{ $t('supporters_only') }}
				</template>
				<template #image="{ src }">
					<m-banner v-if="src" :src="src" url-prefix="users/images"/>
					<div v-else style="height: 300px; width: 100%; background-color: var(--bg-color)"/>
				</template>
			</m-img-uploader>
			<m-input
				v-model="userForm.extra!.background_opacity"
				:disabled="!user.has_supporter_perks"
				:label="true"
				type="range"
				step="0.01"
				min="0"
				max="1"
			>
				<template #label>
					{{ $t('supporter_background_opacity') }} {{ $t('supporters_only') }}
				</template>
			</m-input>

			<m-select v-model="userForm.show_tag" :options="showTagOptions" :label="$t('show_tag')" :desc="$t('show_tag_desc')"/>
			<m-flex>
				<m-input v-model="userForm.donation_url" :validity="donationValid" :label="$t('donation')" :desc="$t('donation_desc')"/>
				<m-input v-model="userForm.custom_title" :label="$t('custom_title')"/>
			</m-flex>
			<m-flex>
				<m-input v-model="userForm.private_profile" :label="$t('private_profile')" :desc="$t('private_profile_desc')" type="checkbox"/>
				<m-input v-model="userForm.invisible" :label="$t('invisible')" :desc="$t('invisible_desc')" type="checkbox"/>
			</m-flex>
		</m-flex>
	</m-form>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~/store';
import type { Mod, User } from '~/types/models';

const { user } = defineProps<{
	user: User;
}>();

const { setUser } = useStore();
const showError = useQuickErrorToast();
const fc = createEventHook();

const { data: pinnedMods } = await useFetchData<Mod[]>(`users/${user.id}/pinned-mods`);

const pinnedModIds: number[] = [];
pinnedMods.value?.forEach(mod => pinnedModIds.push(mod.id));

const userForm = reactive({
	name: user.name,
	bio: user.bio,
	extra: {
		background_opacity: user.extra?.background_opacity
	},
	private_profile: user.private_profile,
	show_tag: user.show_tag,
	donation_url: user.donation_url,
	invisible: user.invisible,
	custom_title: user.custom_title,
	custom_color: user.custom_color,
	pinned_mod_ids: pinnedModIds,
	avatar_file: undefined,
	banner_file: undefined,
	background_file: undefined
});

const isMe = inject<boolean>('isMe');

async function save() {
	try {
		const nextUser = await patchRequest<User>(`users/${user.id}`, serializeObject(userForm));

		if (isMe) {
			setUser(nextUser);
		}

		userForm.avatar_file = undefined;
		userForm.banner_file = undefined;
		userForm.background_file = undefined;

		fc.trigger(userForm);
	} catch (error) {
		showError(error);
	}
}

const { t } = useI18n();
const { settings } = useStore();
const imageSize = computed(() => friendlySize(settings?.image_max_file_size ?? 0));

const donationValid = computed(() => !linkToDonationType(user.donation_url) ? t('donation_invalid') : undefined);

const showTagOptions = [
	{ id: 'role', name: t('show_tag_role') },
	{ id: 'supporter_or_role', name: t('show_tag_supporter_or_role') },
	{ id: 'none', name: t('hide') }
];
</script>
