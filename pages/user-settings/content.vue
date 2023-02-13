<template>
    <a-tabs>
        <template #pre-panels>
            <a-alert class="my-1" type="info">{{$t('content_page_info')}}</a-alert>
        </template>
        <a-tab v-if="user.extra" name="customize" :title="$t('customize')">
            <a-select v-model="user.extra.default_mods_view" :options="viewOptions" label="Default View"/>
            <a-select v-model="user.extra.default_mods_sort" :options="sortOptions" label="Default Sorting"/>
            <h2>Home Page</h2>
            <a-input v-model="user.extra.home_show_last_games" label="Show Last Updated Games" type="checkbox"/>
            <a-input v-model="user.extra.home_show_mods" label="Show Mods" type="checkbox"/>
            <a-input v-model="user.extra.home_show_threads" label="Show Threads" type="checkbox"/>
            <h2>Game Sections</h2>
            <a-input v-model="user.extra.game_show_mods" label="Show Mods" type="checkbox"/>
            <a-input v-model="user.extra.game_show_threads" label="Show Threads" type="checkbox"/>
        </a-tab>
        <a-tab name="follow" :title="$t('following')">
            <h2>{{$t('followed_games')}}</h2>
            <a-items :items="followedGames" :loading="loadingGames" :item-link="item => `g/${item.short_name}`">
                <template #before-item="{ item }">
                    <game-thumbnail :src="item.thumbnail" style="height: 64px;"/>
                </template>
                <template #item-buttons="{ item }">
                    <a-button icon="mdi:remove" @click.prevent="unfollowGame(item)">{{$t('unfollow')}}</a-button>
                </template>
            </a-items>
            <h2>{{$t('followed_users')}}</h2>
            <a-items :items="followedUsers" :loading="loadingUsers">
                <template #item="{ item }">
                    <a-user class="list-button" :user="item">
                        <template #attach>
                            <a-button icon="mdi:remove" class="ml-auto my-auto" @click.prevent="unfollowUser(item)">{{$t('unfollow')}}</a-button>
                        </template>
                    </a-user>
                </template>
            </a-items>            
            <h2>{{$t('followed_mods')}}</h2>
            <a-items :items="followedMods" :loading="loadingMods" :item-link="item => `mod/${item.id}`">
                <template #before-item="{ item }">
                    <mod-thumbnail :thumbnail="item.thumbnail" style="height: 64px;"/>
                </template>
                <template #item-buttons="{ item }">
                    <a-button icon="mdi:remove" @click.prevent="unfollowMod(item)">{{$t('unfollow')}}</a-button>
                </template>
            </a-items>
        </a-tab>
        <a-tab name="block" :title="$t('blocking')">
            <h2>{{$t('blocked_users')}}</h2>
            <a-modal-form v-model="showBlockTag" :title="$t('block_tag')" @submit="submitBlockTag">
                <a-select v-model="blockTag" url="tags" :value-by="false"/>
            </a-modal-form>
            <a-items :items="blockedUsers">
                <template #item="{ item }">
                    <a-user class="list-button" :user="item">
                        <template #attach>
                            <a-button icon="mdi:remove" class="ml-auto my-auto">{{$t('unblock')}}</a-button>
                        </template>
                    </a-user>
                </template>
            </a-items>
            <flex>
                <h2>{{$t('blocked_tags')}}</h2>
                <a-button class="ml-auto" @click="showBlockTag = true">{{$t('block')}}</a-button>
            </flex>
            <a-items :items="blockedTags">
                <template #item-name="{ item }">
                    <a-tag>{{ item.name }}</a-tag>
                </template>
                <template #item-buttons="{ item }">
                    <a-button icon="mdi:remove" @click.prevent="unblockTag(item)">{{$t('unblock')}}</a-button>
                </template>
            </a-items>
        </a-tab>
    </a-tabs>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { useI18n } from 'vue-i18n';
import { Game, Mod, Tag, User, UserForm } from '~~/types/models';
import { setFollowGame, setFollowMod, setFollowUser } from '~~/utils/follow-helpers';

defineProps<{
    user: UserForm
}>();

const { t } = useI18n();

const blockTag = ref<Tag>();
const showBlockTag = ref(false);

const { data: followedGames, loading: loadingGames } = await useWatchedFetchMany('followed-games', { limit: 10 });
const { data: followedUsers, loading: loadingUsers } = await useWatchedFetchMany('followed-users', { limit: 10 });
const { data: followedMods, loading: loadingMods } = await useWatchedFetchMany('followed-mods', { limit: 10 });
const { data: blockedTags, loading: loadingTags } = await useWatchedFetchMany('blocked-tags', { limit: 10 });
const { data: blockedUsers, loading: loadingBlockedUsers } = await useWatchedFetchMany('blocked-users', { limit: 10 });

const viewOptions = [
    { id: 'games', name: t('followed_games') },
    { id: 'mods', name: t('followed_mods') },
    { id: 'users', name: t('followed_users') },
    { id: 'liked', name: t('liked') },
    { id: 'all', name: t('all') },
];

const sortOptions = [
    { id: 'bumped_at', name: t('last_updated') },
    { id: 'published_at', name: t('published_at') },
    { id: 'score', name: t('popular_monthly') },
    { id: 'daily_score', name: t('popular_today') },
    { id: 'weekly_score', name: t('popular_weekly') },
    { id: 'random', name: t('random') },
    { id: 'downloads', name: t('downloads') },
    { id: 'views', name: t('views') },
    { id: 'name', name: t('name') },
    { id: 'likes', name: t('likes') },
];

async function unfollowUser(user: User) {
    await setFollowUser(user, false, false);
    remove(followedUsers.value!.data, user);
}

async function unfollowMod(mod: Mod) {
    await setFollowMod(mod, false, false);
    remove(followedMods.value!.data, mod);
}

async function unfollowGame(game: Game) {
    await setFollowGame(game, false);
    remove(followedGames.value!.data, game);
}

async function unblockTag(tag) {
    try {
        await useDelete(`blocked-tags/${tag.tag_id || tag.id}`);
        remove(blockedTags.value!.data, tag);
    } catch (error) {
        console.log(error);
    }
}

async function submitBlockTag(err) {
    if (!blockTag.value) {
        return;
    }

    try {
        await usePost('blocked-tags', { tag_id: blockTag.value.id });
        if (blockedTags.value) {
            blockedTags.value.data.push(blockTag.value);
        }
        blockTag.value = undefined;
        showBlockTag.value = false;
    } catch (error) {
        err(error);
    }
}
</script>