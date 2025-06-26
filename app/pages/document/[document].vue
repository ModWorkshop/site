<template>
    <page-block v-if="document" :game="game" :breadcrumb="breadcrumb">
        <m-flex gap="3">
            <m-nav side root="/document" >
                <m-nav-link v-for="doc of documents?.data" :key="doc.id" :to="`${doc.url_name}`" :title="doc.name"/>
                <template #content>
                    <h1>{{document.name}}</h1>
                    <m-flex class="items-center">
                        <span :title="$t('last_updated')">
                            <i-mdi-clock/>
                        </span>
                        <i18n-t keypath="by_user_time_ago" scope="global">
                            <template #time>
                                <m-time :datetime="document.updated_at" relative/>
                            </template>
                            <template #user>
                                <a-user :user="document.last_user" avatar-size="xs"/>
                            </template>
                        </i18n-t>
                    </m-flex>
                    <md-content :text="document.desc"/>
                </template>
            </m-nav>
        </m-flex>
    </page-block>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useStore } from '~/store';
import type { Breadcrumb, Document, Game } from '~/types/models';

const { t } = useI18n();
const store = useStore();
const { data: game } = await useResource<Game>('game', 'games');

const { data: documents } = await useFetchMany<Document>(getGameResourceUrl('documents', game.value));

store.setGame(game.value);

const { data: document } = await useResource<Document>('document', 'documents', undefined, {
    game_id: game.value?.id,
});

const breadcrumb = computed(() => {
    const breadcrumb: Breadcrumb[] = [
        { name: t('docs'), attachToPrev: 'documents' },
        { name: document.value.name, id: document.value.id, type: 'document' },
    ];

    if (game.value) {
        breadcrumb.unshift({ name: game.value.name, id: game.value.short_name, type: 'game' });
    }

    return breadcrumb;
});
</script>

<style scoped>
.docs-side {
    align-self: flex-start;
    width: 300px;
}
</style>