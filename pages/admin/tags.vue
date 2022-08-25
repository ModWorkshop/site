<template>
    <a-list url="tags" :item-link="item => `/admin/tags/${item.id}`" new-button="/admin/tags/new">
        <template #default="{ item }">
            <div>
                <a-tag :color="item.color">{{item.name}}</a-tag> 
            </div>
        </template>
    </a-list>
</template>