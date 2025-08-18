<template>
    <m-flex column :class="classes" :gap="gap">
        <img v-if="showBackground && hasBackground" :class="{'background': true}" :style="{
            maskImage: `linear-gradient(rgba(255, 255, 255, ${backgroundOpacity}), transparent)`
        }" :alt="$t('banner')" :src="backgroundUrl">
        <m-flex v-if="breadcrumb || game?.id || gameAnnouncements.length || announcements?.length" class="page-block-nm mx-auto" column gap="3">
            <m-breadcrumb v-if="breadcrumb" :class="breadCrumbClasses" :style="{width: props.backgroundOpacity > 0.2 ? 'initial': null}" :items="breadcrumb"/>
            <game-nav v-if="game?.id" :game="game"/>
            <m-flex v-if="gameAnnouncements.length || announcements?.length" column class="md:flex-row flex-col">
                <announcement-alert v-for="thread of announcements" :key="thread.id" :thread="thread" @hide="hideAnnouncement(thread)"/>
                <announcement-alert v-for="thread of gameAnnouncements" :key="thread.id" :thread="thread" @hide="hideAnnouncement(thread)"/>
            </m-flex>
        </m-flex>
        <m-flex :class="innerClasses" column :gap="gap">
            <slot/>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import { useStore } from '~/store';
import type { Breadcrumb, Game, Thread } from '~/types/models';

const props = withDefaults(defineProps<{
    gap?: number;
    size?: string;
    game?: Game;
    showBackground?: boolean;
    background?: string;
    backgroundOpacity?: number;
    breadcrumb?: Breadcrumb[];
    defineMeta?: boolean;
}>(), { gap: 3, size: 'nm', backgroundOpacity: 0.1, defineMeta: true });

const store = useStore();

const backgroundUrl = computed(() => props.background ?? useSrc('games/images', props.game?.banner));
const hasBackground = computed(() => !!props.background || !!props.game?.banner);

watch(() => props.game, () => {
    store.setGame(props.game || null);
}, { immediate: true });

const { public: config } = useRuntimeConfig();

const breadCrumbClasses = computed(() => {
    if (props.backgroundOpacity > 0.2) {
        return ['content-block', 'content-block-glass', 'self-start' ];
    }
});

const thumbnail = computed(() => {
    const thumb = props.game?.thumbnail;
    if (thumb) {
        return `${config.storageUrl}/games/images/${thumb}`;
    } else {
        return `${config.siteUrl}/assets/no-preview.webp`;
    }
});

if (props.game && props.defineMeta) {
    useServerSeoMeta({
        ogTitle: `ModWorkshop - ${props.game?.name}`,
        ogSiteName: `ModWorkshop - ${props.game?.name}`,
        ogImage: thumbnail.value,
        twitterCard: 'summary',
    });
}

const storedHiddenAns = useCookie('hidden-announcements');
const hiddenAnnouncements = computed(() => {    
    if (storedHiddenAns.value) {
        return storedHiddenAns.value.toString().split(',').map(id => parseInt(id));
    } else {
        return [];
    }
});


const announcements = computed(() => store.announcements?.filter(thread => !hiddenAnnouncements.value.includes(thread.id)));
const gameAnnouncements = computed(() => props.game?.announcements?.filter(thread => !hiddenAnnouncements.value.includes(thread.id)) ?? []);

function hideAnnouncement(thread: Thread) {
    const hidden = hiddenAnnouncements.value;
    hidden.push(thread.id);
    storedHiddenAns.value = hidden.join(',');
}


const classes = computed(() => ({
    'page-block': true,
    'h-full': true,
}));

const innerClasses = computed(() => ({
    'mx-auto': true,
    'page-content': true,
    'page-block-nm': props.size == 'nm',
    'page-block-full': props.size == 'full',
    'page-block-md': props.size == 'md',
    'page-block-sm': props.size == 'sm',
    'page-block-xs': props.size == 'xs',
    'page-block-2xs': props.size == '2xs'
}));

</script>

<style>
.page-content {
    overflow: hidden;
}
</style>

<style>
.page-block {
    padding: 1rem;
    border-radius: 4px;
    width: 100%;
}

/* .page-block:first-child {
    margin-top: 8px;
} */

.page-block-nm, .page-block-full, .page-block-md, .page-block-nm, .page-block-sm, .page-block-xs, .page-block-2xs {
    align-self: center;
}

.page-block-nm {
    width: 81%;
}

.page-block-full {
    width: 100%;
}

.page-block-md {
    width: 75%;
}

.page-block-sm {
    width: 70%;
}

.page-block-xs {
    width: 60%;
}

.page-block-2xs {
    width: 50%;
}
/* 
@media (min-width: 1024px) and (max-width: 1200px) {
    .page-block-nm {
        width: 68%
    }
} */

@media (min-width: 1200px) and (max-width: 1249px) {
    .page-block-nm {
        width: 70%
    }
}

@media (min-width: 1250px) and (max-width: 1349px) {
    .page-block-nm {
        width: 72%
    }
}

@media (min-width: 1350px) and (max-width: 1449px) {
    .page-block-nm {
        width: 74%
    }
}

@media (min-width: 1450px) and (max-width: 1549px) {
    .page-block-nm {
        width: 76%
    }
}

@media (min-width: 1550px) and (max-width: 1699px) {
    .page-block-nm {
        width: 78%
    }
}

@media (min-width: 1700px) and (max-width: 1800px) {
    .page-block-nm {
        width: 81%;
    }
}

@media (max-width:1199px) {
    .page-block, .page-block-md, .page-block-sm, .page-block-xs, .page-block-2xs, .page-block-nm {
        width: 100% !important;
    }
}

.background {
    height: auto;
    top: 56px;
    left: 50%;
    right: 50%;
    width: 100%;
    height: 500px;
    transform: translate(-50%, 0);
    position: absolute;
    object-fit: cover;
    z-index: -1;
}
</style>