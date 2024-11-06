<template>
    <m-flex wrap class="flex-col xl:flex-row list-button items-center !p-4" gap="3">
        <m-img v-if="image" url-prefix="mods/images" :src="image.file" loading="lazy" width="100" height="100"/>
        <m-img v-else src="file-download.webp" is-asset width="100" height="100"/>
        <m-flex grow column style="flex: 1; overflow-wrap: anywhere" gap="2">
            <m-flex class="items-center" style="font-size: 1.15rem;">
                <m-tag v-if="file.label">{{file.label}}</m-tag>
                <strong v-if="file.name" class="items-center">{{file.name}}</strong>
                <strong v-else class="items-center">{{$t(`file_type_${type}`)}}</strong>
            </m-flex>
            <span v-if="file.version" :title="$t('version')">
                <i-mdi-tag/> {{file.version}}
            </span>
            <md-content v-if="file.desc" :text="file.desc" :padding="0"/>
            <m-flex :title="$t('downloads')" class="items-center">
                <i-mdi-download/> <span :title="file.downloads.toString()">{{ friendlyNumber(locale, file.downloads) }}</span>
            </m-flex>
            <m-flex class="items-center" wrap>
                <span :title="$t('upload_date')">
                    <i-mdi-clock/>
                </span>
                <i18n-t keypath="by_user_time_ago" scope="global">
                    <template #time>
                        <m-time :datetime="file.updated_at" relative/>
                    </template>
                    <template #user>
                        <a-user :user="file.user" :avatar="false"/>
                    </template>
                </i18n-t>
            </m-flex>
        </m-flex>
        <m-flex column class="my-auto xl:mx-auto max-xl:w-full" gap="1">
            <mod-download-buttons :mod="mod" :download="file" :type="type"/>
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

const i18n = useI18n();
const locale = computed(() => i18n.locale.value);
const image = computed(() => props.mod.images?.find(image => image.id == props.file.image_id));
</script>