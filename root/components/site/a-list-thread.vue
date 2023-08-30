<template>
    <tr class="cursor-pointer content-block thread" @click.self="clickThread(thread)">
        <td @click.self="clickThread(thread)">
            <i-mdi-pin v-if="!noPins && thread.pinned_at" style="transform: rotate(-45deg);" class="mr-2"/>
            <NuxtLink class="whitespace-pre-line" :to="`/thread/${thread.id}`">{{thread.name}}</NuxtLink>
        </td>
        <td @click.self="clickThread(thread)"><a-user :user="thread.user" @click.stop/></td>
        <td v-if="!noCategory" @click.self="clickThread(thread)">
            <NuxtLink v-if="thread.category" :to="to">{{thread.category.emoji}} {{thread.category.name}}</NuxtLink>
            <span v-else>-</span>
        </td>
        <td @click.self="clickThread(thread)">{{ thread.comment_count }}</td>
        <td @click.self="clickThread(thread)"><time-ago :time="thread.bumped_at"/></td>
        <td v-if="thread.last_user" @click.self="clickThread(thread)"><a-user :user="thread.last_user" @click.stop/></td>
        <td v-else @click.self="clickThread(thread)">{{$t('none')}}</td>
    </tr>
</template>

<script setup lang="ts">
import { Forum, Thread } from '~/types/models';

const { thread, categoryLink } = defineProps<{
    thread: Thread,
    noPins?: boolean,
    forum: Forum,
    categoryLink?: boolean,
    noCategory?: boolean
}>();

const router = useRouter();

const to = computed(() => {
    if (!categoryLink) {
        return undefined;
    }

    return (thread.game ? `/g/${thread.game.short_name}/forum?category=` : '/forum?category=') + thread.category_id; 
});

function clickThread(thread: Thread) {
    console.log('...');
    
    router.push(`/thread/${thread.id}`);
}
</script>