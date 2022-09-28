<template>
    <page-block>
        <a-nav root="/search">
            <a-nav-link to="mods" :title="$t('mods')"/>
            <a-nav-link to="threads" :title="$t('threads')"/>
            <a-nav-link to="users" :title="$t('users')"/>
            <template #content>
                <NuxtPage keepalive/>
            </template>
        </a-nav>
    </page-block>
</template>