<template>
    <m-tabs gap="3" lazy>
        <template #pre-panels>
            <m-alert class="my-1" type="info">{{$t('content_page_info')}}</m-alert>
        </template>
        <m-tab v-if="user.extra" name="customize" :title="$t('customize')">
            <m-select v-model="user.extra.default_mods_view" :options="viewOptions" :label="$t('default_view')"/>
            <m-select v-model="user.extra.default_mods_sort" :options="sortOptions" :label="$t('default_sorting')" default="bumped_at" clearable null-clear/>
            <h2>{{$t('home_page')}}</h2>
            <m-select v-model="user.extra.home_default_mods_sort" :options="sortOptions" :label="$t('default_sorting')" default="daily_score" clearable null-clear/>
            <m-input v-model="user.extra.home_show_last_games" :label="$t('show_last_updated')" type="checkbox"/>
            <m-input v-model="user.extra.home_show_mods" :label="$t('show_mods')" type="checkbox"/>
            <m-input v-model="user.extra.home_show_threads" :label="$t('show_threads')" type="checkbox"/>
            <h2>{{$t('game_sections')}}</h2>
            <m-select v-model="user.extra.game_default_mods_sort" :options="sortOptions" :label="$t('default_sorting')" default="bumped_at" clearable null-clear/>
            <m-input v-model="user.extra.game_show_mods" :label="$t('show_mods')" type="checkbox"/>
            <m-input v-model="user.extra.game_show_threads" :label="$t('show_threads')" type="checkbox"/>
        </m-tab>
        <m-tab name="follow" :title="$t('following')">
            <m-list :limit="10" :title="$t('followed_games')" url="followed-games" :item-link="item => `/g/${item.short_name}`">
                <template #before-item="{ item }">
                    <game-thumbnail :src="item.thumbnail" style="width: 128px; height: 64px;"/>
                </template>
                <template #item-buttons="{ item, items }">
                    <m-button @click.prevent="unfollowGame(item, items.data)"><i-mdi-remove/> {{$t('unfollow')}}</m-button>
                </template>
            </m-list>

            <m-list :limit="10" url="followed-users" :title="$t('followed_users')">
                <template #item="{ item, items }">
                    <a-user class="list-button" :user="item">
                        <template #attach>
                            <m-button class="ml-auto my-auto" @click.prevent="unfollowUser(item, items.data)">
                                <i-mdi-remove/> {{$t('unfollow')}}
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
                    <m-button @click.prevent="unfollowMod(item, items.data)"><i-mdi-remove/> {{$t('unfollow')}}</m-button>
                </template>
            </m-list>
        </m-tab>

        <m-tab name="block" :title="$t('blocking')">
            <m-list :title="$t('blocked_users')" url="blocked-users" :limit="10">
                <template #item="{ item, items }">
                    <a-user class="list-button" :user="item">
                        <template #attach>
                            <m-button class="ml-auto my-auto" @click.prevent="unblockUser(item, items.data)">
                                <i-mdi-remove/> {{$t('unblock')}}
                            </m-button>
                        </template>
                    </a-user>
                </template>
            </m-list>
            <m-list :title="$t('ignored_games')" url="ignored-games" :limit="10">
                <template #before-item="{ item }">
                    <game-thumbnail :src="item.thumbnail" style="width: 128px; height: 64px;"/>
                </template>
                <template #item-buttons="{ item, items }">
                    <m-button @click.prevent="unignoreGame(item, items.data)"><i-mdi-remove/> {{$t('unignore')}}</m-button>
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
                    <m-button class="ml-auto" @click="showBlockTag = true">{{$t('block')}}</m-button>
                </template>
                <template #item-buttons="{ item, items }">
                    <m-button @click.prevent="unblockTag(item, items.data)"><i-mdi-remove/> {{$t('unblock')}}</m-button>
                </template>
            </m-list>
        </m-tab>
    </m-tabs>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import { useI18n } from 'vue-i18n';
import type { Game, Mod, Tag, User, UserForm } from '~/types/models';
import { setFollowGame, setFollowMod, setFollowUser } from '~/utils/follow-helpers';

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

async function unignoreGame(game: Game, ignoredGames: Game[]) {
    await setIgnoreGame(game, false);
    remove(ignoredGames, game);
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