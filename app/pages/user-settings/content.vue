<template>
    <m-tabs>
        <template #pre-panels>
            <m-alert class="my-1" type="info">{{$t('content_page_info')}}</m-alert>
        </template>
        <m-tab v-if="user.extra" name="customize" :title="$t('customize')">
            <m-select v-model="user.extra.default_mods_view" :options="viewOptions" :label="$t('default_view')"/>
            <m-select v-model="user.extra.default_mods_sort" :options="sortOptions" :label="$t('default_sorting')"/>
            <h2>{{$t('home_page')}}</h2>
            <m-input v-model="user.extra.home_show_last_games" :label="$t('show_last_updated')" type="checkbox"/>
            <m-input v-model="user.extra.home_show_mods" :label="$t('show_mods')" type="checkbox"/>
            <m-input v-model="user.extra.home_show_threads" :label="$t('show_threads')" type="checkbox"/>
            <h2>{{$t('game_sections')}}</h2>
            <m-input v-model="user.extra.game_show_mods" :label="$t('show_mods')" type="checkbox"/>
            <m-input v-model="user.extra.game_show_threads" :label="$t('show_threads')" type="checkbox"/>
        </m-tab>
        <m-tab name="follow" :title="$t('following')">
            <m-list 
                v-model:page="followedGamesPage"
                :items="followedGames"
                :limit="10"
                :loading="loadingGames"
                :title="$t('followed_games')"
                :item-link="item => `/g/${item.short_name}`"
            >
                <template #before-item="{ item }">
                    <game-thumbnail :src="item.thumbnail" style="width: 128px; height: 64px;"/>
                </template>
                <template #item-buttons="{ item }">
                    <m-button @click.prevent="unfollowGame(item)"><i-mdi-remove/> {{$t('unfollow')}}</m-button>
                </template>
            </m-list>

            <m-list 
                v-model:page="followedUsersPage"
                :items="followedUsers"
                :limit="10"
                :loading="loadingUsers"
                :title="$t('followed_users')"
            >
                <template #item="{ item }">
                    <a-user class="list-button" :user="item">
                        <template #attach>
                            <m-button class="ml-auto my-auto" @click.prevent="unfollowUser(item)">
                                <i-mdi-remove/> {{$t('unfollow')}}
                            </m-button>
                        </template>
                    </a-user>
                </template>
            </m-list>
            <m-list 
                v-model:page="followedModsPage"
                :title="$t('followed_mods')"
                :items="followedMods"
                :limit="10"
                :loading="loadingMods"
                :item-link="item => `/mod/${item.id}`"
            >
                <template #before-item="{ item }">
                    <mod-thumbnail :thumbnail="item.thumbnail" style="width: 128px; height: 64px;"/>
                </template>
                <template #item-buttons="{ item }">
                    <m-button @click.prevent="unfollowMod(item)"><i-mdi-remove/> {{$t('unfollow')}}</m-button>
                </template>
            </m-list>
        </m-tab>

        <m-tab name="block" :title="$t('blocking')">
            <m-form-modal v-model="showBlockTag" :title="$t('block_tag')" @submit="submitBlockTag">
                <m-select v-model="blockTag" url="tags" list-tags color-by="color" :value-by="false"/>
            </m-form-modal>
            <m-list 
                v-model:page="blockedUsersPage"
                :title="$t('blocked_users')"
                :items="blockedUsers"
                :limit="10"
                :loading="loadingBlockedUsers"
            >
                <template #item="{ item }">
                    <a-user class="list-button" :user="item">
                        <template #attach>
                            <m-button class="ml-auto my-auto" @click.prevent="unblockUser(item)">
                                <i-mdi-remove/> {{$t('unblock')}}
                            </m-button>
                        </template>
                    </a-user>
                </template>
            </m-list>
            <m-list 
                v-model:page="blockedTagsPage"
                :title="$t('blocked_tags')"
                :items="blockedTags"
                :limit="10"
                :loading="loadingTags"
            >
                <template #item-name="{ item }">
                    <m-tag>{{ item.name }}</m-tag>
                </template>
                <template #buttons>
                    <m-button class="ml-auto" @click="showBlockTag = true">{{$t('block')}}</m-button>
                </template>
                <template #item-buttons="{ item }">
                    <m-button @click.prevent="unblockTag(item)"><i-mdi-remove/> {{$t('unblock')}}</m-button>
                </template>
            </m-list>
        </m-tab>
    </m-tabs>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import { useI18n } from 'vue-i18n';
import type { Game, Mod, Tag, User, UserForm } from '~/types/models';

defineProps<{
    user: UserForm
}>();

const isMe = inject<boolean>('isMe');

if (!isMe) {
    useNoPermsPage();
}

const { t } = useI18n();

const blockTag = ref<Tag>();
const showBlockTag = ref(false);
const showError = useQuickErrorToast();
const followedGamesPage = ref(1);
const followedUsersPage = ref(1);
const followedModsPage = ref(1);
const blockedTagsPage = ref(1);
const blockedUsersPage = ref(1);

const { data: followedGames, loading: loadingGames } = await useWatchedFetchMany('followed-games', { limit: 10, page: followedGamesPage });
const { data: followedUsers, loading: loadingUsers } = await useWatchedFetchMany('followed-users', { limit: 10, page: followedUsersPage });
const { data: followedMods, loading: loadingMods } = await useWatchedFetchMany('followed-mods', { limit: 10, page: followedModsPage });
const { data: blockedTags, loading: loadingTags } = await useWatchedFetchMany('blocked-tags', { limit: 10, page: blockedTagsPage });
const { data: blockedUsers, loading: loadingBlockedUsers } = await useWatchedFetchMany('blocked-users', { limit: 10, page: blockedUsersPage });

const viewOptions = [
    { id: 'followed', name: t('followed') },
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

async function unblockTag(tag: Tag) {
    try {
        await deleteRequest(`blocked-tags/${tag.id}`);
        remove(blockedTags.value!.data, tag);
    } catch (error) {
        showError(error);
    }
}

async function unblockUser(user: User) {
    try {
        await deleteRequest(`blocked-users/${user.id}`);
        remove(blockedUsers.value!.data, user);
    } catch (error) {
        showError(error);
    }
}

async function submitBlockTag(err) {
    if (!blockTag.value) {
        return;
    }

    try {
        await postRequest('blocked-tags', { tag_id: blockTag.value.id });
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