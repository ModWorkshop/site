<template>
    <flex column :class="classes" :gap="gap">
        <flex class="page-block-nm mx-auto" column gap="4">
            <flex v-if="game?.id" gap="0" column>
                <a-banner v-if="gameBanner" :src="game.banner" url-prefix="games/banners" style="height: 250px"/>
                <content-block :column="false" wrap class="items-center" gap="4">
                    <h2 class="my-auto mb-1">
                        <a-link-button :to="`/g/${game.short_name}`">{{game.name}}</a-link-button>
                    </h2>
                    <flex wrap gap="4">
                        <a-link-button v-if="!store.user || !store.isBanned" v-once :to="`/g/${game.short_name}/upload`">{{$t('upload_mod')}}</a-link-button>
                        <a-link-button :to="`/g/${game.short_name}/mods`">{{$t('mods')}}</a-link-button>
                        <a-link-button :to="`/g/${game.short_name}/forum`">{{$t('forum')}}</a-link-button>
                        <a-link-button v-for="button in buttons" :key="button[0]" class="nav-item" :href="button[1]">{{button[0]}}</a-link-button>
                    </flex>
                    <flex class="ml-auto items-center" gap="4">
                        <flex v-if="store.gameBan" v-once column>
                            <span class="text-danger">
                                <a-icon icon="triangle-exclamation"/> {{$t('banned')}}
                            </span>
                            <span>
                                <i18n-t keypath="expires_t">
                                    <template #time>
                                        <time-ago :time="store.gameBan.expire_date"/>
                                    </template>
                                </i18n-t>
                            </span>
                        </flex>
                    </flex>
                    <flex class="ml-auto" gap="2" wrap>
                        <flex class="mr-4" gap="2">
                            <NuxtLink v-if="canSeeReports" :title="$t('reports')" :class="{'text-warning': hasReports, 'text-body': !hasReports}" :to="`/g/${game.id}/admin/reports`">
                                <a-icon icon="mdi:alert-box"/> {{reportsCount}}
                            </NuxtLink>
                            <NuxtLink v-if="canSeeWaiting" :title="$t('approvals')" :class="{'text-warning': hasWaiting, 'text-body': !hasWaiting}" :to="`/g/${game.id}/admin/approvals`">
                                <a-icon icon="mdi:clock"/> {{waitingCount}}
                            </NuxtLink>
                            <a-link-button v-if="canSeeAdminGamePage" icon="mdi:cogs" :to="`/g/${game.id}/admin`">{{$t('admin_page')}}</a-link-button>
                        </flex>

                        <a-link-button v-if="store.user" icon="mdi:account-settings-variant" :to="`/g/${game.short_name ?? game.id}/user/${store.user.id}`">{{$t('game_preferences')}}</a-link-button>
                        <a-link-button v-if="store.user" :icon="game.followed ? 'mdi:minus-thick' : 'mdi:plus-thick'" @click="setFollowGame(game!)">{{$t(game.followed ? 'unfollow' : 'follow')}}</a-link-button>
                        <a-link-button v-else icon="mdi:plus-thick" to="/login">{{$t('follow')}}</a-link-button>
                    </flex>
                </content-block>
            </flex>
            <flex column gap="2">
                <template v-if="announcements.length">
                    <h4>📢 {{$t('announcements')}}</h4>
                    <flex class="md:flex-row flex-col">
                        <a-announcement v-for="thread of announcements" :key="thread.id" :thread="thread" @hide="hideAnnouncement(thread)"/>
                    </flex>
                </template>
                <template v-if="gameAnnouncements.length">
                    <h4 >📢 {{$t('game_announcements')}}</h4>
                    <flex class="md:flex-row flex-col">
                        <a-announcement v-for="thread of gameAnnouncements" :key="thread.id" :thread="thread" @hide="hideAnnouncement(thread)"/>
                    </flex>
                </template>
            </flex>
        </flex>
        <flex :class="innerClasses" column :gap="gap">
            <the-breadcrumb v-if="breadcrumb" :items="breadcrumb"/>
            <slot/>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Breadcrumb, Game, Thread } from '~~/types/models';
import { setFollowGame } from '~~/utils/follow-helpers';
import { adminGamePagePerms } from '~~/utils/helpers';

const props = withDefaults(defineProps<{
    gap?: number;
    size?: string;
    game?: Game;
    gameBanner?: boolean;
    breadcrumb?: Breadcrumb[];
}>(), { gap: 3, size: 'nm' });

const store = useStore();

watch(() => props.game, () => {
    if (props.game) {
        store.setGame(props.game);
    }
}, { immediate: true });

const storedHiddenAns = useCookie('hidden-announcements');
const hiddenAnnouncements = computed(() => {    
    if (storedHiddenAns.value) {
        return storedHiddenAns.value.toString().split(',').map(id => parseInt(id));
    } else {
        return [];
    }
});

const reportsCount = computed(() => props.game!.reports_count ?? 0);
const waitingCount = computed(() => props.game!.waiting_count ?? 0);
const hasReports = computed(() => reportsCount.value > 0);
const hasWaiting = computed(() => waitingCount.value > 0);
const canSeeReports = computed(() => store.hasPermission('manage-users', props.game));
const canSeeWaiting = computed(() => store.hasPermission('manage-mods', props.game));

const announcements = computed(() => store.announcements.filter(thread => !hiddenAnnouncements.value.includes(thread.id)));
const gameAnnouncements = computed(() => props.game?.announcements?.filter(thread => !hiddenAnnouncements.value.includes(thread.id)) ?? []);
const canSeeAdminGamePage = computed(() => props.game && adminGamePagePerms.some(perm => store.hasPermission(perm, props.game)));

function hideAnnouncement(thread: Thread) {
    const hidden = hiddenAnnouncements.value;
    hidden.push(thread.id);
    storedHiddenAns.value = hidden.join(',');
}

const buttons = computed(() => {
    if (props.game) {
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

.page-block-nm {
    width: 83%;
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

@media (max-width:1024px) {
    .page-block, .page-block-md, .page-block-sm, .page-block-xs, .page-block-2xs {
        width: 100%;
    }
}
</style>