<template>
    <m-flex wrap class="flex-col md:flex-row list-button items-center" gap="3">
        <div>
            <m-img v-if="image" url-prefix="mods/images" :src="image.file" loading="lazy" width="150" height="150"/>
            <m-img v-else src="file-download.webp" is-asset width="150" height="150"/>
        </div>
        <m-flex grow column style="flex: 1;" gap="2">
            <m-flex class="items-center">
                <m-tag v-if="file.label">{{file.label}}</m-tag>
                <strong v-if="file.name" class="items-center">{{file.name}}</strong>
                <strong v-else class="items-center">{{$t(`file_type_${type}`)}}</strong>
            </m-flex>
            <span v-if="file.version" :title="$t('version')">
                <i-mdi-tag/> {{file.version}}
            </span>
            <md-content v-if="file.desc" :text="file.desc"/>
            <m-flex class="items-center">
                <span :title="$t('upload_date')">
                    <i-mdi-clock/>
                </span>
                <i18n-t keypath="by_user_time_ago" scope="global">
                    <template #time>
                        <m-time-ago :time="file.created_at"/>
                    </template>
                    <template #user>
                        <a-user :user="file.user" avatar-size="xs"/>
                    </template>
                </i18n-t>
            </m-flex>
        </m-flex>
        <m-flex column class="my-auto mx-auto" gap="1">
            <mod-download-buttons :mod="mod" :download="file"/>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import type { File, Link, Mod } from '~~/types/models';

const props = defineProps<{
    file: File & Link,
    type: 'file'|'link',
    mod: Mod
}>();

const image = computed(() => props.mod.images?.find(image => image.id == props.file.image_id));
</script>