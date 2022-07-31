<template>
    <flex column gap="3" class="mod-info content-block self-start">
        <mod-thumbnail :mod="mod"/>
        <flex column class="p-4" gap="4">
            <flex gap="2">
                <span>
                    <font-awesome-icon icon="heart"/> {{likes}}
                </span>
                <span>
                    <font-awesome-icon icon="download"/> {{downloads}}
                </span>
                <span>
                    <font-awesome-icon icon="eye"/> {{views}}
                </span>
                <span class="ml-auto">
                    <font-awesome-icon v-if="mod.published_at" icon="calendar-plus" :title="$t('published_at')"/> <time-ago :time="mod.published_at"/>
                </span>
            </flex>

            <flex v-if="mod.tags.length > 0">
                <a-tag v-for="tag in mod.tags" :key="tag.id" :color="tag.color">{{tag.name}}</a-tag>
            </flex>

            <flex class="colllaborators-block" wrap>
                <a-user :user="mod.submitter" :details="$t('submitter')"/>
                <a-user v-for="member of members" :key="member.id" :user="member" :details="levels[member.level]"/>
            </flex>
        </flex>
    </flex>
</template>

<script setup lang="ts">
    import { Mod } from '~~/types/models';

    const levels = [
        'Maintainer',
        'Collaborator',
        'Viewer',
        'Contributor',
    ];

    const props = defineProps<{
        mod: Mod
    }>();

    const members = computed(() => props.mod.members.filter(member => member.accepted && member.level !== 2));

    const likes = computed(() => props.mod.likes);
    const downloads = computed(() => props.mod.downloads);
    const views = computed(() => props.mod.views);
</script>