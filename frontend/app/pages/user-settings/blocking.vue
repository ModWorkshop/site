<template>
	<m-flex column gap="3">
		<m-alert class="my-1" type="info">{{ $t('content_page_info') }}</m-alert>

		<m-list :title="$t('blocked_users')" url="blocked-users" :limit="10">
			<template #item="{ item, items }">
				<a-user class="list-button" :user="item">
					<template #attach>
						<m-button class="ml-auto my-auto" @click.prevent="unblockUser(item, items.data)">
							<i-mdi-remove/> {{ $t('unblock') }}
						</m-button>
					</template>
				</a-user>
			</template>
		</m-list>
		<m-list :title="$t('ignored_games')" url="ignored-games" :limit="10">
			<template #before-item="{ item }">
				<game-thumbnail :game="item" style="width: 128px; height: 64px;"/>
			</template>
			<template #item-buttons="{ item, items }">
				<m-button @click.prevent="unignoreGame(item, items.data)"><i-mdi-remove/> {{ $t('unignore') }}</m-button>
			</template>
		</m-list>
		<m-list :title="$t('ignored_categories')" url="ignored-categories" :limit="10">
			<template #buttons="{ items }">
				<m-form-modal v-model="showIgnoreCategory" :title="$t('ignore_category')" @submit="err => submitIgnoreCategory(err, items.data)">
					<game-select v-model="ignoreCategoryGameId" :label="$t('game')" clearable/>
					<category-select
						v-if="ignoreCategoryGameId"
						v-model="ignoreCategoryId"
						:max-height="500"
						:categories="categories?.data ?? []"
						:label="$t('category')"
					/>
				</m-form-modal>
				<m-button class="ml-auto" @click="showIgnoreCategory = true">{{ $t('ignore') }}</m-button>
			</template>
			<template #item-name="{ item }">
				<span class="self-center">
					{{ item.path }}
				</span>
			</template>
			<template #item-buttons="{ item, items }">
				<m-button @click.prevent="unignoreCategory(item, items.data)"><i-mdi-remove/> {{ $t('unignore') }}</m-button>
			</template>
		</m-list>
		<m-list :title="$t('ignored_mods')" url="ignored-mods" :limit="10">
			<template #before-item="{ item }">
				<game-thumbnail :game="item" style="width: 128px; height: 64px;"/>
			</template>
			<template #item-buttons="{ item, items }">
				<m-button @click.prevent="unignoreMod(item, items.data)"><i-mdi-remove/> {{ $t('unignore') }}</m-button>
			</template>
		</m-list>
		<m-list :title="$t('blocked_tags')" url="blocked-tags" :limit="10">
			<template #item-name="{ item }">
				<m-tag>{{ item.name }}</m-tag>
			</template>
			<template #buttons="{ items }">
				<m-form-modal v-model="showBlockTag" :title="$t('block_tag')" @submit="err => submitBlockTag(err, items.data)">
					<m-select v-model="blockTag" url="tags" list-tags color-by="color" :value-by="false"/>
				</m-form-modal>
				<m-button class="ml-auto" @click="showBlockTag = true">{{ $t('block') }}</m-button>
			</template>
			<template #item-buttons="{ item, items }">
				<m-button @click.prevent="unblockTag(item, items.data)"><i-mdi-remove/> {{ $t('unblock') }}</m-button>
			</template>
		</m-list>
	</m-flex>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import type { Game, Category, Mod, Tag, User } from '~/types/models';

const isMe = inject<boolean>('isMe');

if (!isMe) {
	useNoPermsPage();
}

const blockTag = ref<Tag>();
const showBlockTag = ref(false);
const showIgnoreCategory = ref(false);
const showError = useQuickErrorToast();

const ignoreCategoryGameId = ref();
const ignoreCategoryId = ref();

const { data: categories, refresh } = await useFetchMany<Category>('categories', {
	immediate: false,
	query: {
		include_paths: true,
		game_id: ignoreCategoryGameId
	}
});

watch(ignoreCategoryGameId, refresh);

async function unignoreGame(game: Game, ignoredGames: Game[]) {
	await setIgnoreGame(game, false);
	remove(ignoredGames, game);
}

async function unignoreCategory(cat: Category, ignoredCats: Category[]) {
	try {
		await deleteRequest(`ignored-categories/${cat.id}`);
		remove(ignoredCats, cat);
	} catch (error) {
		showError(error);
	}
}

async function unignoreMod(mod: Mod, ignoredMods: Mod[]) {
	await setIgnoreMod(mod, false);
	remove(ignoredMods, mod);
}

async function unblockTag(tag: Tag, blockedTags: Tag[]) {
	try {
		await deleteRequest(`blocked-tags/${tag.id}`);
		remove(blockedTags, tag);
	} catch (error) {
		showError(error);
	}
}

async function unblockUser(user: User, blockedUsers: User[]) {
	try {
		await deleteRequest(`blocked-users/${user.id}`);
		remove(blockedUsers, user);
	} catch (error) {
		showError(error);
	}
}

async function submitIgnoreCategory(err, ignoredCats: Category[]) {
	if (!ignoreCategoryId.value) {
		return;
	}

	try {
		await postRequest('ignored-categories', { category_id: ignoreCategoryId.value });
		const cat = categories.value?.data.find(cat => cat.id === ignoreCategoryId.value);
		if (cat) {
			ignoredCats.push(cat);
		}
		ignoreCategoryId.value = undefined;
		showIgnoreCategory.value = false;
	} catch (error) {
		err(error);
	}
}

async function submitBlockTag(err, blockedTags: Tag[]) {
	if (!blockTag.value) {
		return;
	}

	try {
		await postRequest('blocked-tags', { tag_id: blockTag.value.id });
		blockedTags.push(blockTag.value);
		blockTag.value = undefined;
		showBlockTag.value = false;
	} catch (error) {
		err(error);
	}
}
</script>
