<template>
    <simple-resource-form v-model="tag" url="tags" :game="game" :redirect-to="redirectTo">
        <div>
            <m-tag :color="tag.color">{{tag.name}}</m-tag>
        </div>
        <m-input v-model="tag.name" :label="$t('name')"/>
        <m-input v-model="tag.color" :label="$t('color')" type="color"/>
        <m-select v-model="tag.type" :options="types" :label="$t('type')"/>
        <md-editor v-model="tag.notice" :label="$t('tag_notice')" :desc="$t('tag_notice_desc')"/>
        <m-select v-model="tag.notice_type" :options="noticeTypes" :label="$t('tag_notice_type')"/>
        <m-input v-if="!props.game" v-model="tag.notice_localized" :label="$t('tag_notice_localized')" type="checkbox"/>
    </simple-resource-form>
</template>

<script setup lang="ts">
import { useI18n } from "vue-i18n";
import type { Game, Tag } from "~~/types/models";

const props = defineProps<{
    game: Game
}>();

useNeedsPermission('manage-tags', props.game);

const { t } = useI18n();

const types = [
    { name: t('all'), id: '' },
    { name: t('threads'), id: 'forum' },
    { name: t('mods'), id: 'mod' },
];

const noticeTypes = [
    { name: t('tag_notice_info'), id: 'info' },
    { name: t('tag_notice_warn'), id: 'warning' },
    { name: t('tag_notice_danger'), id: 'danger' },
];

const redirectTo = computed(() => getAdminUrl('tags', props.game));

const { data: tag } = await useEditResource<Tag>('tag', 'tags', {
    id: 0,
    name: '',
    color: '#fff',
    notice: '',
    type: '',
    notice_type: 'info',
    game_id: props.game?.id,
    notice_localized: true
});
</script>