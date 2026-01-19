<template>
	<simple-resource-form
		v-model="vmGame"
		:can-save="canSaveOverride"
		:merge-params="mergeParams"
		url="games"
		redirect-to="/g"
		delete-redirect-to="/games"
		:delete-button="canDelete"
		@submit="submit"
	>
		<m-img-uploader id="thumbnail" v-model="thumbnailBlob" :label="$t('thumbnail')" :src="vmGame.thumbnail">
			<template #image="{ src }">
				<game-thumbnail :src="src" :game="vmGame" style="width: 250px;"/>
			</template>
		</m-img-uploader>
		<m-img-uploader id="banner" v-model="bannerBlob" :label="$t('banner')" :src="vmGame.banner">
			<template #image="{ src }">
				<m-banner :src="src" url-prefix="games/images"/>
			</template>
		</m-img-uploader>
		<m-flex>
			<m-input v-model="vmGame.name" :label="$t('name')"/>
			<m-input v-model="vmGame.short_name" :label="$t('short_name')"/>
		</m-flex>
		<m-input v-model="vmGame.buttons" :label="$t('game_buttons')" :desc="$t('game_buttons_desc')"/>
		<m-input v-model="vmGame.webhook_url" :label="$t('webhook_url')" desc="Whenever a new mod is published to this category, the site will call this webhook (generally Discord)"/>
		<m-flex class="items-center">
			<m-select v-model="vmGame.default_mod_manager_id" :options="modManagers?.data" :label="$t('default_mod_manager')" :desc="$t('default_mod_manager_desc')"/>
			<m-select v-model="vmGame.mod_manager_ids" :options="globalModManagers" multiple :label="$t('applied_global_mod_managers')" :desc="$t('applied_global_mod_managers_desc')"/>
		</m-flex>
		<m-select v-model="vmGame.hidden_tag_ids" :options="tags?.data" multiple color-by="color" list-tags :label="$t('hidden_tags')" :desc="$t('hidden_tags_desc')"/>
	</simple-resource-form>
</template>

<script setup lang="ts">
import type { Game, ModManager, Tag } from '~/types/models';
import { useStore } from '~/store/index';

const thumbnailBlob = ref();
const bannerBlob = ref();
const canSaveOverride = computed(() => !!(thumbnailBlob.value || bannerBlob.value));
const { hasPermission } = useStore();

const vmGame = defineModel<Game>({ required: true });
const canDelete = computed(() => hasPermission('manage-games'));
const mmUrl = getGameResourceUrl('mod-managers', vmGame.value);

const { data: modManagers } = await useFetchMany<ModManager>(() => mmUrl, {
	params: {
		global: true,
		show_hidden: true
	}
});

const { data: tags } = await useFetchMany<Tag>('tags', {
	params: {
		global: true
	}
});

const globalModManagers = computed(() => modManagers.value?.data.filter(mm => !mm.game_id));

const mergeParams = reactive({
	thumbnail_file: thumbnailBlob,
	banner_file: bannerBlob
});

function submit() {
	thumbnailBlob.value = null;
	bannerBlob.value = null;
}
</script>
