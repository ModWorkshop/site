<template>
    <page-block size="sm">
        <the-breadcrumb :items="breadcrumb"/>
        <a-form>
            <content-block class="p-8">
                <a-input v-model="thread.name" :label="$t('title')"/>
                <md-editor v-model="thread.content" :label="$t('content')"/>
                <a-select v-model="thread.category_id" :label="$t('category')" :options="categories.data"/>
                <flex class="mx-auto">
                    <a-button type="submit" @click="submit">{{$t('post')}}</a-button>
                </flex>
            </content-block>
        </a-form>
    </page-block>
</template>

<script setup lang="ts">
import { Ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { ForumCategory, Game, Thread } from '~~/types/models';


const route = useRoute();
const router = useRouter();

const { init: showToast } = useToast();
const { t } = useI18n();

let game: Ref<Game>;

if (route.params.id) {
    const { data, error } = await useFetchData<Game>(`games/${route.params.id}`);
    useHandleError(error, {
        404: 'This game does not exist!'
    });

    game = data;
}

const forumId = game ? game.value.forum.id : 1;

const thread = ref({
    id: 0,
    name: '',
    content: '',
    category_id: null,
    forum_id: forumId,
});

const { data: categories } = await useFetchMany<ForumCategory>('forum-categories', {
    params: {
        forum_id: forumId
    }
});

async function submit() {
    if (thread.value.id) {
      //TODO  
    } else {
        const newThread = await usePost<Thread>(`threads`, thread.value);
        if (newThread) {
            router.push(`/thread/${newThread.id}`);
        } else {
            showToast({ color: 'danger', message: 'Something went wrong' });
        }
    }
}

const breadcrumb = computed(() => {
    let crumbs;

    if (game) {
        crumbs = [
            { name: game.value.name, id: game.value.short_name },
            { name: 'forum', attachToPrev: 'forum' },
        ];
    } else {
        crumbs = [
            { name: 'forum', href: `forum` }
        ];
    }

    crumbs.unshift({ name: thread.value.id ? thread.value.name : t('post') });

    return crumbs;
});
</script>