<template>
    <m-flex column :class="classes" :gap="gap">
        <img v-if="showBackground && hasBackground" :class="{'background': true}" :style="{
            maskImage: `linear-gradient(rgba(255, 255, 255, ${backgroundOpacity}), transparent)`
        }" :alt="$t('banner')" :src="backgroundUrl">
        <m-flex v-if="breadcrumb || game?.id || gameAnnouncements.length || announcements?.length" class="page-block-nm mx-auto" column gap="3">
            <m-breadcrumb v-if="breadcrumb" :class="breadCrumbClasses" :style="{width: props.backgroundOpacity > 0.2 ? 'initial': null}" :items="breadcrumb"/>
            <m-flex v-if="game?.id" gap="0" column>
                <m-link class="h2 my-6 mx-2" style="font-weight: 800;" :to="`/g/${game.short_name}`">{{game.name}}</m-link>
                <m-content-block :column="false" wrap class="items-center content-block-glass" gap="4">
                    <m-flex wrap gap="4">
                        <m-link v-if="!store.isBanned" v-once :to="user ? `/g/${game.short_name}/upload` : '/login'">
                            <i-mdi-upload/> {{$t('upload_mod')}}
                        </m-link>
                        <m-link :to="`/g/${game.short_name}/mods`"><i-mdi-puzzle/> {{$t('browse_mods')}}</m-link>
                        <m-link :to="`/g/${game.short_name}/forum`"><i-mdi-forum/> {{$t('forum')}}</m-link>
                        <m-link v-for="button in buttons" :key="button[0]" class="nav-item" :href="button[1]">{{button[0]}}</m-link>
                    </m-flex>
                    <m-flex class="md:ml-auto items-center" gap="2" wrap>
                        <m-flex class="mr-4" gap="2">
                            <m-flex v-if="store.gameBan" v-once gap="0" column>
                                <span class="text-danger">
                                    <i-mdi-alert/> {{$t('banned')}}
                                </span>
                                <span>
                                    <i18n-t keypath="expires_t" scope="global">
                                        <template #time>
                                            <m-time-ago :time="store.gameBan.expire_date"/>
                                        </template>
                                    </i18n-t>
                                </span>
                            </m-flex>

                            
                            <NuxtLink v-if="canSeeReports" :title="$t('reports')" :class="{'text-warning': hasReports, 'text-body': !hasReports}" :to="`/g/${game.short_name}/admin/reports`">
                                <i-mdi-alert-box/> {{reportCount}}
                            </NuxtLink>
                            <NuxtLink v-if="canSeeWaiting" :title="$t('approvals')" :class="{'text-warning': hasWaiting, 'text-body': !hasWaiting}" :to="`/g/${game.short_name}/admin/approvals`">
                                <i-mdi-clock/> {{waitingCount}}
                            </NuxtLink>
                            <m-link v-if="canSeeAdminGamePage" :to="`/g/${game.short_name}/admin`"><i-mdi-cogs/> {{$t('admin_page')}}</m-link>
                        </m-flex>

                        <m-link v-if="store.user" :to="`/g/${game.short_name ?? game.id}/user/${store.user.id}`">
                            <i-mdi-account-settings-variant/> {{$t('game_preferences')}}
                        </m-link>
                        <m-link v-if="store.user" @click="setFollowGame(game!)">
                            <i-mdi-minus-thick v-if="game.followed"/>
                            <i-mdi-plus-thick v-else/>
                            {{$t(game.followed ? 'unfollow' : 'follow')}}
                        </m-link>
                        <m-link v-else to="/login"><i-mdi-plus-thick/> {{$t('follow')}}</m-link>
                    </m-flex>
                </m-content-block>
            </m-flex>
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
import { useStore } from '~~/store';
import type { Breadcrumb, Game, Thread } from '~~/types/models';
import { setFollowGame } from '~~/utils/follow-helpers';
import { adminGamePagePerms } from '~~/utils/helpers';
import { storeToRefs } from 'pinia';

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
const { user } = storeToRefs(store);

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

const reportCount = computed(() => props.game!.report_count ?? 0);
const waitingCount = computed(() => props.game!.waiting_count ?? 0);
const hasReports = computed(() => reportCount.value > 0);
const hasWaiting = computed(() => waitingCount.value > 0);
const canSeeReports = computed(() => store.hasPermission('manage-users', props.game));
const canSeeWaiting = computed(() => store.hasPermission('manage-mods', props.game));

const announcements = computed(() => store.announcements?.filter(thread => !hiddenAnnouncements.value.includes(thread.id)));
const gameAnnouncements = computed(() => props.game?.announcements?.filter(thread => !hiddenAnnouncements.value.includes(thread.id)) ?? []);
const canSeeAdminGamePage = computed(() => props.game && adminGamePagePerms.some(perm => store.hasPermission(perm, props.game)));

function hideAnnouncement(thread: Thread) {
    const hidden = hiddenAnnouncements.value;
    hidden.push(thread.id);
    storedHiddenAns.value = hidden.join(',');
}

const buttons = computed(() => {
    if (props.game && props.game.buttons) {
        const btns = props.game.buttons.split(',');
        const res: string[][] = [];
    
        for (const btn of btns) {
            res.push(btn.split('|'));
        }
    
        return res;
    }
});

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
    width: 82%;
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


@media (min-width: 1024px) and (max-width: 1200px) {
    .page-block-nm {
        width: 68%
    }
}

@media (min-width: 1200px) and (max-width: 1400px) {
    .page-block-nm {
        width: 74%
    }
}

@media (min-width: 1400px) and (max-width: 1600px) {
    .page-block-nm {
        width: 77%
    }
}

@media (min-width: 1600px) and (max-width: 1700px) {
    .page-block-nm {
        width: 80%
    }
}

@media (max-width:1279px) {
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