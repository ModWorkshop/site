<template>
    <flex column :class="classes" :gap="gap">
        <flex v-if="game?.id" gap="0" column>
            <a-banner v-if="gameBanner" :src="game.banner" url-prefix="games/banners" style="height: 250px">
                <h2 v-if="!game.banner" class="ml-2 align-self-end">{{game.name}}</h2>
            </a-banner>
            <content-block :column="false" wrap class="items-center" gap="4">
                <a-link-button class="text-3xl" :to="`/g/${game.short_name}`">{{game.name}}</a-link-button>
                <flex wrap gap="4">
                    <a-link-button v-if="!store.user || !store.isBanned" v-once :to="`/g/${game.short_name}/upload`">{{$t('upload_mod')}}</a-link-button>
                    <a-link-button :to="`/g/${game.short_name}/forum`">{{$t('forum')}}</a-link-button>
                    <a-link-button :to="`/g/${game.short_name}/mods`">{{$t('mods')}}</a-link-button>
                    <a-link-button v-for="button in buttons" :key="button[0]" class="nav-item" :href="button[1]">{{button[0]}}</a-link-button>
                </flex>
                <flex class="ml-auto items-center" gap="4">
                    <flex v-if="store.gameBan" v-once column>
                        <span class="text-danger">
                            <a-icon icon="triangle-exclamation"/> Banned
                        </span>
                        <span>
                            <i18n-t keypath="expires_t">
                                <template #time>
                                    <time-ago :time="store.gameBan.case.expire_date"/>
                                </template>
                            </i18n-t>
                        </span>
                    </flex>
                </flex>
                <flex class="ml-auto" gap="4">
                    <a-link-button icon="mdi:cog" :to="`/user-settings?game=${game.id}`">{{$t('game_settings')}}</a-link-button>
                    <a-link-button v-if="canSeeAdminGamePage" icon="mdi:cogs" :to="`/admin/games/${game.id}`">{{$t('game_admin_page')}}</a-link-button>
                    <a-link-button :icon="game.followed ? 'mdi:minus-thick' : 'mdi:plus-thick'" @click="setFollowGame(game)">{{$t(game.followed ? 'unfollow' : 'follow')}}</a-link-button>
                </flex>
            </content-block>
        </flex>
        <the-breadcrumb v-if="breadcrumb" :items="breadcrumb"/>
        <flex v-if="announcements.length" column>
            <h4>📢 Announcements</h4>
            <a-announcement v-for="thread of announcements" :key="thread.id" :thread="thread" @hide="hideAnnouncement(thread)"/>
        </flex>
        <flex v-if="gameAnnouncements.length" column>
            <h4>📢 Game Announcements</h4>
            <a-announcement v-for="thread of gameAnnouncements" :key="thread.id" :thread="thread" @hide="hideAnnouncement(thread)"/>
        </flex>
        <slot/>
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
}>(), { gap: 3 });

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


const announcements = computed(() => store.announcements.filter(thread => !hiddenAnnouncements.value.includes(thread.id)));
const gameAnnouncements = computed(() => store.currentGame?.announcements?.filter(thread => !hiddenAnnouncements.value.includes(thread.id)) ?? []);
const canSeeAdminGamePage = computed(() => props.game && adminGamePagePerms.some(perm => store.hasPermission(perm, props.game)));

function hideAnnouncement(thread: Thread) {
    const hidden = hiddenAnnouncements.value;
    hidden.push(thread.id);
    storedHiddenAns.value = hidden.join(',');
}

const buttons = computed(() => {
    if (props.game) {
        const btns = props.game.buttons.split(',');
        const res = [];
    
        for (const btn of btns) {
            res.push([...btn.split('|')]);
        }
    
        return res;
    }
});

const classes = computed(() => ({
    'page-block': true,
    'h-full': true,
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
    width: 83%;
}

/* .page-block:first-child {
    margin-top: 8px;
} */

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