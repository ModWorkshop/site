<template>
    <m-content-block class="p-8 page-block-sm">
        <simple-resource-form 
            v-model="thread"
            url="threads"
            redirect-to="/thread"
            :create-url="`forums/${forumId}/threads`"
            :delete-redirect-to="deleteRedirectTo"
            :can-save="thumbnailFile != undefined"
            :merge-params="mergeParams"
            assign-object
            :captcha="!thread.id"
            @submit="submit()"
        >
            <m-img-uploader
                v-if="currentCategory?.grid_mode"
                v-model="thumbnailFile"
                :label="$t('thumbnail')"
                clear-button
                :src="thread.thumbnail"
                :max-file-size="settings?.image_max_file_size"
            >
                <template #image="{ src }">
                    <a-thumbnail :src="src" style="width: 250px;" url-prefix="threads/images"/>
                </template>
            </m-img-uploader>

            <m-input v-model="thread.name" :label="$t('title')" minlength="2" maxlength="150"/>
            <md-editor v-model="thread.content" minlength="2" maxlength="5000" :label="$t('content')"/>
            <m-select v-model="thread.category_id" :label="$t('category')" :options="allowedCategories" clearable/>
            <m-select v-model="thread.tag_ids" :options="tags?.data" multiple list-tags color-by="color" :label="$t('tags')"/>
            <template v-if="canManage">
                <m-input v-model="thread.announce" type="checkbox" :label="$t('announce')"/>
                <m-duration v-if="thread.announce" v-model="thread.announce_until" :label="$t('announcement_duration')"/>
            </template>
        </simple-resource-form>
    </m-content-block>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useStore } from '~~/store';
import type { ForumCategory, Game, Tag, Thread } from '~~/types/models';
import clone from 'rfdc/default';

const { game } = defineProps<{
    game?: Game;
}>();

const thumbnailFile = ref();
const store = useStore();
const { settings } = useStore();

const { isBanned, ban, gameBan, user } = storeToRefs(store);
const categoryId = useRouteQuery('category');

const forumId = game ? game.forum_id : 1;

const initialThread = defineModel<Thread>();
const thread = ref(clone(initialThread.value) ?? {
    id: 0,
    views: 0,
    locked: false,
    locked_by_mod: false,
    announce: false,
    name: '',
    content: '',
    tag_ids: [],
    user_id: user.value!.id,
    forum_id: forumId,
    category_id: categoryId.value ? parseInt(categoryId.value) : undefined
});

const g = game ?? thread.value?.forum?.game;
store.setGame(g);

if (!useCanEditThread(thread.value, g)) {
    useNoPermsPage();
}

const canManage = computed(() => store.hasPermission('manage-discussions', game));

const { data: tags } = await useFetchMany<Tag>('tags', { 
    params: { 
        game_id: game?.id,
        type: 'forum',
        global: 1
    }
});

const { data: categories } = await useFetchMany<ForumCategory>('forum-categories', {
    params: {
        forum_id: thread.value.forum_id
    },
    cacheData: true
});

const allowedCategories = computed(() => {
    const canAppeal = ban.value?.can_appeal ?? true;
    const canAppealGame = gameBan.value?.can_appeal ?? true;

    return categories.value?.data.filter(cat => cat.can_post && (!isBanned.value || (canAppeal && canAppealGame))) ?? [];
});

const currentCategory = computed(() => allowedCategories.value.find(c => c.id == thread.value.category_id));

const mergeParams = reactive({
    thumbnail_file: thumbnailFile,
});

if (!thread.value.id && isBanned.value && !allowedCategories.value.length) {
    useNoPermsPage();
}

const threadGame = computed(() => game ?? (thread.value.forum ? thread.value.forum.game : null));

const deleteRedirectTo = computed(() => threadGame.value ? `/g/${threadGame.value.short_name}/forum` : `/forum`);

function submit() {
    thumbnailFile.value = undefined;
}
</script>