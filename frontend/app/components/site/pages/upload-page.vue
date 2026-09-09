<template>
	<m-content-block class="p-8 page-block-xs">
		<h2>{{ $t('upload_mod') }}</h2>
		<m-form v-model="mod" :created="false" float-save-gui :flush-changes="fc" @submit="save">
			<m-flex column gap="3">
				<Title>{{ $t('upload_mod') }}</Title>
				<m-flex v-if="!mod.id" gap="2" column>
					<m-alert :title="$t('edit_mod_tips_title')" color="info">
						{{ $t('upload_mod_tip') }}
					</m-alert>
					<m-alert v-if="newUserWarn" :title="$t('edit_mod_warns_title')" color="warning">
						{{ $t('edit_mod_warn_new_user') }}
					</m-alert>
				</m-flex>
				<m-input v-model="mod.name" placeholder="My Cool Mod" :label="$t('name')" maxlength="100" minlength="3" required :desc="$t('mod_name_desc')"/>

				<md-editor v-model="mod.desc" :label="$t('description')" :desc="$t('mod_desc_help')" minlength="3" required rows="12"/>

				<game-select v-if="!game" v-model="mod.game_id" :label="$t('game')" required/>

				<category-select v-if="categories?.data.length" v-model="mod.category_id" :label="$t('category')" :desc="$t('category_desc')" :categories="categories.data"/>

				<span class="text-center">
					<i18n-t keypath="upload_mod_rules_tos" scope="global">
						<template #rules>
							<NuxtLink to="/document/rules">{{ $t('rules') }}</NuxtLink>
						</template>
						<template #tos>
							<NuxtLink to="/document/terms">{{ $t('terms') }}</NuxtLink>
						</template>
					</i18n-t>
				</span>
			</m-flex>
		</m-form>
	</m-content-block>
</template>

<script setup lang="ts">
import type { Category, Game, Mod, Tag } from '~/types/models';
import { useStore } from '~/store/index';

const store = useStore();

const { game } = defineProps<{
	game?: Game;
}>();

if (game) {
	store.currentGame = game;
}

const mod = ref<Mod>({
	id: 0,
	name: '',
	desc: '',
	images: [],
	members: [],
	tag_ids: [],
	short_desc: '',
	changelog: '',
	license: '',
	instructions: '',
	donation: '',
	legacy_banner_url: '',
	game_id: game?.id ?? 0,
	version: '',
	user_id: 0,
	user: store.user!,
	downloads: 0,
	likes: 0,
	views: 0,
	visibility: 'public',
	suspended: false,
	comments_disabled: false,
	approved: false,
	has_download: false,
	disable_mod_managers: false,
	files_are_versions: true,
	custom_version: ''
});

const { setGame, settings, user: me } = useStore();
const showErrorToast = useQuickErrorToast();
const router = useRouter();
const queryTab = useRouteQuery('tab');
const fc = createEventHook();

const newUserWarn = computed(() => settings?.new_user_first_upload_requires_approval && me!.needs_mod_approval);

watch(() => mod.value.game, () => {
	if (mod.value.game) {
		setGame(mod.value.game);
	}
}, { immediate: true });

const gameId = computed(() => mod.value.game_id || undefined);
const { data: categories, refresh: refetchCats } = await useFetchMany<Category>(() => `games/${gameId.value}/categories`, { immediate: !!gameId.value });

watch(() => categories.value, () => {
	if (categories.value && categories.value.data.length === 0) {
		mod.value.category_id = undefined;
	}
});

watch(gameId, val => {
	if (val) {
		refetchCats();
		refreshTags();
	}
});

async function save() {
	try {
		fc.trigger(await postRequest<Mod>(`/games/${mod.value.game_id}/mods`, mod.value));
		router.replace({ path: `/mod/${mod.value.id}/edit`, query: { tab: queryTab.value || undefined } });
	} catch (error) {
		showErrorToast(error);
		return;
	}
}
</script>
