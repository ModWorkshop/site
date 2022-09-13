<template>
    <a-list url="games" query :item-link="item => `/admin/games/${item.id}`" new-button="/admin/games/new">
        <template #before-item="{ item }">
            <game-thumbnail :src="item.thumbnail" style="width: 100px;"/>
        </template>
    </a-list>
</template>