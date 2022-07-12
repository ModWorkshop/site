<template>
    <flex column gap="3" class="mod-info content-block p-2 self-start">
        <div class="thumbnail overflow-hidden ratio-image-mod-thumb">
            <mod-thumbnail :mod="mod"/>
        </div>
        <flex column class="p-2" gap="4">
            <flex style="font-size: 1.5rem;" gap="3">
                <flex inline>
                    <font-awesome-icon icon="heart"/> <span id="likes">{{likes}}</span>
                </flex>
                <flex inline>
                    <font-awesome-icon icon="download"/> {{downloads}}
                </flex>
                <flex inline>
                    <font-awesome-icon icon="eye"/> {{views}}
                </flex>
            </flex>
            <flex v-if="mod.tags.length > 0">
                <!-- TODO: Don't forget to make them link -->
                <a-tag v-for="tag in mod.tags" :key="tag.id" :color="tag.color">{{tag.name}}</a-tag>
            </flex>
            <div class="colllaborators-block">
                <a-user avatarSize="medium" :user="mod.submitter" :details="$t('submitter')"/>
            </div>
        </flex>
    </flex>
</template>

<script setup lang="ts">
    import { Mod } from '~~/types/models';

    defineProps<{
        mod: Mod
    }>();

    const likes = computed(() => 0);
    const downloads = computed(() => 0);
    const views = computed(() => 0);
</script>