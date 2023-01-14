<template>
    <flex v-if="!mod.has_download || mod.approved !== true || mod.suspended" column gap="2">
        <the-tag-notices v-if="mod.tags" :tags="mod.tags"/>
        <a-alert v-if="mod.suspended" color="danger" :title="$t('suspended')">
            <i18n-t keypath="mod_suspended" tag="span">
                <template #reason>
                    <span v-if="mod.last_suspension">
                        {{mod.last_suspension.reason}}
                    </span>
                    <span v-else>{{$t('no_reason')}}</span>
                </template>
                <template #rules>
                    <NuxtLink to="/rules">{{$t('rules').toLowerCase()}}</NuxtLink>
                </template>
                <template #forum>
                    <NuxtLink :to="`/game/${mod.game?.short_name}/forum?category=appeals`">{{$t('forum').toLowerCase()}}</NuxtLink>
                </template>
            </i18n-t>
        </a-alert>
        <a-alert v-if="!mod.has_download" color="warning" :title="$t('downloads_alert')" :desc="$t('downloads_alert_desc')"/>
        <a-alert v-else-if="mod.approved === null" color="info" :title="$t('mod_waiting')">
            <span>
                {{$t('mod_waiting_desc')}}
            </span>
            <mod-approve v-if="canManage" :mod="mod"/>
        </a-alert>
        <a-alert v-else-if="mod.approved === false" color="danger" :title="$t('mod_rejected')" :desc="$t('mod_rejected_desc')"/>
    </flex>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { Mod } from '~~/types/models';
const props = defineProps<{
    mod: Mod
}>();

const { hasPermission } = useStore();
const canManage = computed(() => hasPermission('manage-mod', props.mod.game));
</script>