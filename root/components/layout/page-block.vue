<template>
    <flex column :class="classes" :gap="gap">
        <flex class="page-block-nm mx-auto" column gap="3">
            <the-breadcrumb v-if="breadcrumb" :items="breadcrumb"/>
            <flex v-if="game?.id" gap="0" column>
                <img v-if="gameBanner" :class="{'game-banner': true}" :src="bannerUrl">
                <content-block :column="false" wrap class="items-center" gap="4">
                    <h2 class="my-auto mb-1">
                        <a-link-button :to="`/g/${game.short_name}`">{{game.name}}</a-link-button>
                    </h2>
                    <flex wrap gap="4">
                        <a-link-button v-if="!store.isBanned" v-once icon="mdi:upload" :to="user ? `/g/${game.short_name}/upload` : '/login'">{{$t('upload_mod')}}</a-link-button>
                        <a-link-button :to="`/g/${game.short_name}/mods`" icon="mdi:puzzle">{{$t('browse_mods')}}</a-link-button>
                        <a-link-button :to="`/g/${game.short_name}/forum`" icon="mdi:forum">{{$t('forum')}}</a-link-button>
                        <a-link-button v-for="button in buttons" :key="button[0]" :icon="button[2]" class="nav-item" :href="button[1]">{{button[0]}}</a-link-button>
                    </flex>
                    <flex class="ml-auto items-center" gap="2" wrap>
                        <flex class="mr-4" gap="2">
                            <flex v-if="store.gameBan" v-once gap="0" column>
                                <span class="text-danger">
                                    <a-icon icon="triangle-exclamation"/> {{$t('banned')}}
                                </span>
                                <span>
                                    <i18n-t keypath="expires_t" scope="global">
                                        <template #time>
                                            <time-ago :time="store.gameBan.expire_date"/>
                                        </template>
                                    </i18n-t>
                                </span>
                            </flex>

                            
                            <NuxtLink v-if="canSeeReports" :title="$t('reports')" :class="{'text-warning': hasReports, 'text-body': !hasReports}" :to="`/g/${game.short_name}/admin/reports`">
                                <a-icon icon="mdi:alert-box"/> {{reportCount}}
                            </NuxtLink>
                            <NuxtLink v-if="canSeeWaiting" :title="$t('approvals')" :class="{'text-warning': hasWaiting, 'text-body': !hasWaiting}" :to="`/g/${game.short_name}/admin/approvals`">
                                <a-icon icon="mdi:clock"/> {{waitingCount}}
                            </NuxtLink>
                            <a-link-button v-if="canSeeAdminGamePage" icon="mdi:cogs" :to="`/g/${game.short_name}/admin`">{{$t('admin_page')}}</a-link-button>
                        </flex>

                        <a-link-button v-if="store.user" icon="mdi:account-settings-variant" :to="`/g/${game.short_name ?? game.id}/user/${store.user.id}`">{{$t('game_preferences')}}</a-link-button>
                        <a-link-button v-if="store.user" :icon="game.followed ? 'mdi:minus-thick' : 'mdi:plus-thick'" @click="setFollowGame(game!)">{{$t(game.followed ? 'unfollow' : 'follow')}}</a-link-button>
                        <a-link-button v-else icon="mdi:plus-thick" to="/login">{{$t('follow')}}</a-link-button>
                    </flex>
                </content-block>
            </flex>
            <flex v-if="gameAnnouncements.length || announcements?.length" column class="md:flex-row flex-col">
                <a-announcement v-for="thread of announcements" :key="thread.id" :thread="thread" @hide="hideAnnouncement(thread)"/>
                <a-announcement v-for="thread of gameAnnouncements" :key="thread.id" :thread="thread" @hide="hideAnnouncement(thread)"/>
            </flex>
        </flex>
        <flex :class="innerClasses" column :gap="gap">
            <slot/>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Breadcrumb, Game, Thread } from '~~/types/models';
import { setFollowGame } from '~~/utils/follow-helpers';
import { adminGamePagePerms } from '~~/utils/helpers';
import { storeToRefs } from 'pinia';

const props = withDefaults(defineProps<{
    gap?: number;
    size?: string;
    game?: Game;
    gameBanner?: boolean;
    breadcrumb?: Breadcrumb[];
    defineMeta?: boolean;
}>(), { gap: 3, size: 'nm', defineMeta: true });

const store = useStore();
const { user } = storeToRefs(store);

const bannerUrl = computed(() => useSrc('games/images', props.game?.banner));

watch(() => props.game, () => {
    store.setGame(props.game || null);
}, { immediate: true });

const { public: config } = useRuntimeConfig();

const thumbnail = computed(() => {
    const thumb = props.game?.thumbnail;
    if (thumb) {
        return `${config.storageUrl}/games/images/${thumb}`;
    } else {
        return  `${config.siteUrl}/assets/no-preview-dark.png`;
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
    'page-block-nm': props.size == 'nm',
    'page-block-full': props.size == 'full',
    'page-block-md': props.size == 'md',
    'page-block-sm': props.size == 'sm',
    'page-block-xs': props.size == 'xs',
    'page-block-2xs': props.size == '2xs'
}));

</script>

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

@media (max-width:1024px) {
    .page-block, .page-block-md, .page-block-sm, .page-block-xs, .page-block-2xs, .page-block-nm {
        width: 100% !important;
    }
}

.game-banner {
    height: auto;
    -webkit-mask-image: linear-gradient(rgba(255, 255, 255, 0.1), transparent);
    mask-image: linear-gradient(rgba(255, 255, 255, 0.1), transparent);
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