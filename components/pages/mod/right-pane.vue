<template>
    <flex column gap="1" class="mod-info content-block p-2">
        <div class="thumbnail overflow-hidden ratio-image-mod-thumb">
            <mod-thumbnail :mod="mod"/>
        </div>
        <div class="p-2" style="font-size: 20px">
            <div class="p-1 inline-block">
                <font-awesome-icon icon="heart"/>
                <span id="likes">{{likes}}</span>
            </div>
            <div class="p-1 inline-block">
                <font-awesome-icon icon="download"/>
                <span>{{downloads}}</span>
            </div>
            <div class="p-1 inline-block">
                <font-awesome-icon icon="eye"/>
                <span>{{views}}</span>
            </div>
        </div>
        <flex gap="1" wrap class="p-2 tags-block">
            <!-- TODO: Don't forget to make them link -->
            <a-tag v-for="tag in mod.tags" :key="tag.id" :color="tag.color">{{tag.name}}</a-tag>
        </flex>
        <div class="p-2 colllaborators-block">
            <a-user avatarSize="medium" :user="mod.submitter" :details="$t('submitter')"/>
        </div>
    </flex>
</template>

<script setup>
    import { computed } from '@nuxtjs/composition-api';

    defineProps({
        mod: Object
    });

    const likes = computed(() => 0);
    const downloads = computed(() => 0);
    const views = computed(() => 0);
</script>

<style lang="scss" scoped>

</style>