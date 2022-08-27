<template>
    <page-block>
        <the-breadcrumb v-if="game" :items="breadcrumb"/>
        <flex>
            <a-button :to="newThreadLink">New Thread</a-button>
        </flex>
        <flex style="flex: 1;" gap="3">
            <content-block style="flex: 1;">
                <a-input v-model="query" label="Search"/>
                <flex column gap="2">
                    <template v-if="categories.data.length">
                        <a class="nav-link" @click="categoryName = undefined">
                            All
                        </a>
                        <a v-for="category of categories.data" :key="category.name" class="nav-link cursor-pointer" @click="categoryName = category.name">
                            {{category.name}}
                        </a>
                    </template>
                    <span v-else>
                        No categories found!
                    </span>
                </flex>
            </content-block>
            <content-block style="flex: 4;">
                <a-table v-if="threads" :background="false">
                    <template #head>
                        <th>Title</th>
                        <th>Poster</th>
                        <th v-if="!categoryName">Category</th>
                        <th>Last activity</th>
                        <th>Last Reply</th>
                    </template>
                    <tr v-for="thread in threads.data" :key="thread.created_at" class="cursor-pointer" @click="clickThread(thread)">
                        <td><NuxtLink :to="`/thread/${thread.id}`">{{thread.name}}</NuxtLink></td>
                        <td><a-user :user="thread.user"/></td>
                        <td><a>{{thread.category.name}}</a></td>
                        <td><time-ago :time="thread.bumped_at"/></td>
                        <td v-if="thread.last_user"><a-user :user="thread.last_user"/></td>
                        <td v-else>None</td>
                    </tr>
                </a-table>
            </content-block>
        </flex>
    </page-block>
</template>

<script setup lang="ts">
import { ForumCategory, Game, Thread } from '~~/types/models';

const router = useRouter();
const query = ref('');
const categoryName = useRouteQuery('category');

const { data: game } = await useResource<Game>('game', 'games');

const forumId = game.value ? game.value.forum_id : 1;

const { data: threads } = await useFetchMany<Thread>('threads', {
    params: {
        forum_id: forumId,
        category_name: categoryName,
        query: query
    }
});

const breadcrumb = computed(() => {
    return [
        { name: game.value.name, id: game.value.short_name, type: 'game' },
        { name: 'forum' },
    ];
});

const { data: categories } = await useFetchMany<ForumCategory>('forum-categories', {
    params: {
        forum_id: forumId
    }
});

const newThreadLink = computed(() => game.value ? `/g/${game.value.short_name}/forum/post` : '/forum/post');

function clickThread(thread: Thread) {
    router.push(`/thread/${thread.id}`);
}
</script>