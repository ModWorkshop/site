<template>
    <page-block>
        <the-breadcrumb :items="breadcrumb"/>
        <content-block padding="6">
            <h1>{{document.name}}</h1>
            <flex class="items-center">
                Last edited by <a-user :user="document.last_user" avatar-size="xs"/> <time-ago :time="document.updated_at"/>
            </flex>
            <a-markdown :text="document.desc"/>
        </content-block>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~~/store';
import { Breadcrumb, Document, Game } from '~~/types/models';

const { t } = useI18n();
const store = useStore();
const { data: game } = await useResource<Game>('game', 'games');

store.setGame(game.value);

const { data: document } = await useResource<Document>('document', 'documents', null, {
    game_id: game.value?.id,
});

const breadcrumb = computed(() => {
    const breadcrumb: Breadcrumb[] = [
        { name: t('docs'), attachToPrev: 'docs' },
        { name: document.value.name, id: document.value.id, type: 'document' },
    ];

    if (game.value) {
        breadcrumb.unshift({ name: game.value.name, id: game.value.short_name, type: 'game' });
    }

    return breadcrumb;
});
</script>