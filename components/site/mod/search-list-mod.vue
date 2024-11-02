<template>
    <m-flex gap="2" inline class="items-center align-middle cursor-pointer" @click="modUrl ? $router.push(modUrl) : undefined">
        <NuxtLink :to="modUrl">
            <mod-thumbnail :thumbnail="mod?.thumbnail" style="height: 64px;"/>
        </NuxtLink>
        <m-flex column>
            <template v-if="mod">
                <NuxtLink :to="modUrl">
                    {{mod.name}}
                </NuxtLink>
                <a-user :user="mod.user" :static="static" avatar-size="sm" :show-mini-profile="false" @click="onClickUser"/>
            </template>
            <template v-else>
                <NuxtLink :to="url">
                    {{name}}
                </NuxtLink>
            </template>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import type { Mod } from "~~/types/models";

const props = defineProps<{
    mod?: Mod,
    url?: string,
    name?: string,
    static?: boolean,
}>();

const modUrl = computed(() => {
    if (!props.static) {
        return props.url ?? `/mod/${props.mod?.id}`;
    }
});

function onClickUser(e) {
    if (!props.static) {
        e.stopPropagation();
    }
}
</script>