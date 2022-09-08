<template>
    <a-list url="mods" query :item-link="item => `/mod/${item.id}/edit`" :params="{ game_id: $route.params.gameId }">
        <template #before-item="{ item }">
            <mod-thumbnail :thumbnail="item.thumbnail" style="width: 100px;"/>
        </template>
    </a-list>
</template>