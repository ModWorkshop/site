<template>
    <tr class="cursor-pointer content-block thread">
        <td @click.self="clickThread(thread)">
            <a-icon v-if="!noPins && thread.pinned_at" style="transform: rotate(-45deg);" class="mr-2" icon="thumbtack"/>
            <NuxtLink :to="`/thread/${thread.id}`">{{thread.name}}</NuxtLink>
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
    console.log('...');
    
    router.push(`/thread/${thread.id}`);
}
</script>