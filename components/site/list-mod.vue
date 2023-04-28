<template>
    <flex gap="2" inline class="items-center align-middle cursor-pointer" @click="$router.push(modUrl)">
        <NuxtLink :to="modUrl" @click.stop>
            <mod-thumbnail :thumbnail="mod?.thumbnail" style="height: 64px;"/>
        </NuxtLink>
        <flex column>
            <template v-if="mod">
                <NuxtLink :to="`/mod/${mod?.id}`" @click.stop>
                    {{mod.name}}
                </NuxtLink>
                <a-user avatar-size="sm" :user="mod.user" @click.stop/>
            </template>
            <template v-else>
                <NuxtLink :to="url" @click.stop>
                    {{name}}
                </NuxtLink>
            </template>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { Mod } from "~~/types/models";

const props = defineProps<{
    mod?: Mod,
    url?: string,
    name?: string,
}>();

const modUrl = computed(() => props.url ?? `/mod/${props.mod?.id}`);

</script>