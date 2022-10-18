<template>
    <a-tabs>
        <template #pre-panels>
            <a-alert class="my-1" type="info">{{$t('content_page_info')}}</a-alert>
        </template>
        <a-tab name="customize" :title="$t('customize')">
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
            <a-list v-model="followedGames" url="followed-games" limit="10" :item-link="item => `g/${item.short_name}`">
                <template #before-item="{ item }">
                    <game-thumbnail :src="item.thumbnail" style="height: 64px;"/>
                </template>
                <template #item-buttons="{ item }">
                    <a-button icon="remove" @click="unfollowGame(item)">{{$t('unfollow')}}</a-button>
                </template>
            </a-list>
            <h2>{{$t('followed_users')}}</h2>
            <a-list v-model="followedUsers" url="followed-users" limit="10">
                <template #item="{ item }">
                    <a-user class="list-button" :user="item">
                        <template #attach>
                            <a-button icon="remove" class="ml-auto my-auto" @click="unfollowUser(item)">{{$t('unfollow')}}</a-button>
                        </template>
                    </a-user>
                </template>
            </a-list>            
            <h2>{{$t('followed_mods')}}</h2>
            <a-list v-model="followedMods" url="followed-mods" limit="10" :item-link="item => `mod/${item.id}`">
                <template #before-item="{ item }">
                    <mod-thumbnail :thumbnail="item.thumbnail" style="height: 64px;"/>
                </template>
                <template #item-buttons="{ item }">
                    <a-button icon="remove" @click="unfollowMod(item)">{{$t('unfollow')}}</a-button>
                </template>
            </a-list>
        </a-tab>
        <a-tab name="block" :title="$t('blocking')">
            <h2>{{$t('blocked_users')}}</h2>
            <a-modal-form v-model="showBlockTag" :title="$t('block_tag')" @submit="submitBlockTag">
                <a-select v-model="blockTag" url="tags" :value-by="false"/>
            </a-modal-form>
            <a-list url="blocked-users" limit="10">
                <template #item="{ item }">
                    <a-user class="list-button" :user="item">
                        <template #attach>
                            <a-button icon="remove" class="ml-auto my-auto">{{$t('unblock')}}</a-button>
                        </template>
                    </a-user>
                </template>
            </a-list>
            <h2>{{$t('blocked_tags')}}</h2>
            <a-list v-model="blockedTags" url="blocked-tags" limit="10">
                <template #buttons>
                    <a-button @click="showBlockTag = true">{{$t('block')}}</a-button>
                </template>
                <template #item-buttons="{ item }">
                    <a-button icon="remove" @click="unblockTag(item)">{{$t('unblock')}}</a-button>
                </template>
            </a-list>
        </a-tab>
    </a-tabs>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { useI18n } from 'vue-i18n';
import { Game, Mod, Tag, User, UserForm } from '~~/types/models';
import { Paginator } from '~~/types/paginator';
import { setFollowGame, setFollowMod, setFollowUser } from '~~/utils/follow-helpers';

defineProps<{
    user: UserForm
}>();

const { t } = useI18n();

const blockTag = ref<Tag>(null);
const showBlockTag = ref(false);

const blockedTags = ref<Paginator<Tag>>(null);
const followedUsers = ref<Paginator<User>>(null);
const followedMods = ref<Paginator<Mod>>(null);
const followedGames = ref<Paginator<Game>>(null);

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
    await setFollowUser(user, false);
    remove(followedUsers.value.data, user);
}

async function unfollowMod(mod: Mod) {
    await setFollowMod(mod, false);
    remove(followedMods.value.data, mod);
}

async function unfollowGame(game: Game) {
    await setFollowGame(game);
    remove(followedGames.value.data, game);
}

async function unblockTag(tag) {
    try {
        await useDelete(`blocked-tags/${tag.tag_id || tag.id}`);
        remove(blockedTags.value.data, tag);
    } catch (error) {
        console.log(error);
    }
}

async function submitBlockTag(ok, err) {
    try {
        await usePost('blocked-tags', { tag_id: blockTag.value.id });
        blockedTags.value.data.push(blockTag.value);
        blockTag.value = null;
        ok();
    } catch (error) {
        err(error);
    }
}
</script>