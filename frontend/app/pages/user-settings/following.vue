<template>
	<m-flex column gap="3">
		<m-alert class="my-1" type="info">{{ $t('content_page_info') }}</m-alert>

		<m-list :limit="10" :title="$t('followed_games')" url="followed-games" :item-link="item => `/g/${item.short_name}`">
			<template #before-item="{ item }">
				<game-thumbnail :game="item" style="width: 128px; height: 64px;"/>
			</template>
			<template #item-buttons="{ item, items }">
				<m-button @click.prevent="unfollowGame(item, items.data)"><i-mdi-remove/> {{ $t('unfollow') }}</m-button>
			</template>
		</m-list>

		<m-list :limit="10" url="followed-users" :title="$t('followed_users')">
			<template #item="{ item, items }">
				<a-user class="list-button" :user="item">
					<template #attach>
						<m-button class="ml-auto my-auto" @click.prevent="unfollowUser(item, items.data)">
							<i-mdi-remove/> {{ $t('unfollow') }}
						</m-button>
					</template>
				</a-user>
			</template>
		</m-list>
		<m-list :title="$t('followed_mods')" :limit="10" url="followed-mods" :item-link="item => `/mod/${item.id}`">
			<template #before-item="{ item }">
				<mod-thumbnail :thumbnail="item.thumbnail" style="width: 128px; height: 64px;"/>
			</template>
			<template #item-buttons="{ item, items }">
				<m-button @click.prevent="unfollowMod(item, items.data)"><i-mdi-remove/> {{ $t('unfollow') }}</m-button>
			</template>
		</m-list>
	</m-flex>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import type { Game, Mod, User } from '~/types/models';
import { setFollowGame, setFollowMod, setFollowUser } from '~/utils/follow-helpers';

const isMe = inject<boolean>('isMe');

if (!isMe) {
	useNoPermsPage();
}

async function unfollowUser(user: User, followedUsers: User[]) {
	await setFollowUser(user, false, false);
	remove(followedUsers, user);
}

async function unfollowMod(mod: Mod, followedMods: Mod[]) {
	await setFollowMod(mod, false, false);
	remove(followedMods, mod);
}

async function unfollowGame(game: Game, followedGames: Game[]) {
	await setFollowGame(game, false);
	remove(followedGames, game);
}
</script>
