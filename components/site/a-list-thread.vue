<template>
    <tr class="cursor-pointer content-block thread" @click.self="clickThread(thread)">
        <td>
            <a-icon v-if="!noPins && thread.pinned_at" style="transform: rotate(-45deg);" class="mr-2" icon="thumbtack"/>
            <NuxtLink :to="`/thread/${thread.id}`">{{thread.name}}</NuxtLink>
        </td>
        <td><a-user :user="thread.user" @click.stop/></td>
        <td v-if="!noCategory">
            <NuxtLink v-if="thread.category" :to="to">{{thread.category.emoji}} {{thread.category.name}}</NuxtLink>
            <span v-else>-</span>
        </td>
        <td>{{ thread.comment_count }}</td>
        <td><time-ago :time="thread.bumped_at"/></td>
        <td v-if="thread.last_user"><a-user :user="thread.last_user" @click.stop/></td>
        <td v-else>{{$t('none')}}</td>
    </tr>
</template>

<script setup lang="ts">
import { Thread } from '~/types/models';

const props = defineProps<{
    thread: Thread,
    noPins?: boolean,
    categoryLink?: boolean,
    noCategory?: boolean,
}>();

const router = useRouter();

const forum = computed(() => props.thread?.forum);
const game = computed(() => forum.value?.game);

const to = computed(() => {
    if (!forum.value) {
        return;
    }

    return (game.value ? `/g/${game.value.short_name}/forum?category=` : '/forum?category=') + props.thread.category_id; 
});

function clickThread(thread: Thread) {
    router.push(`/thread/${thread.id}`);
}
</script>