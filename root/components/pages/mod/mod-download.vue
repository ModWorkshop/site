<template>
    <flex wrap class="flex-col md:flex-row list-button">
        <div class="mr-2">
            <a-img v-if="image" url-prefix="mods/images" :src="image.file" width="128" height="128"/>
            <a-img v-else src="file-download.webp" is-asset width="128" height="128"/>
        </div>
        <flex grow column style="flex: 1;">
            <a-tag v-if="file.label" class="mr-auto">{{file.label}}</a-tag>
            <h3 v-if="file.name">{{file.name}}</h3>
            <h3 v-else>{{$t(`file_type_${type}`)}}</h3>
            <span v-if="file.version">
                <a-icon icon="tag" :title="$t('version')"/> {{file.version}}
            </span>
            <a-markdown v-if="file.desc" class="mt-3" :text="file.desc"/>
            <flex class="items-center mt-auto">
                <a-icon icon="clock" :title="$t('upload_date')"/>
                <i18n-t keypath="by_user_time_ago" scope="global">
                    <template #time>
                        <time-ago :time="file.created_at"/>
                    </template>
                    <template #user>
                        <a-user :user="file.user" avatar-size="xs"/>
                    </template>
                </i18n-t>
            </flex>
        </flex>
        <div class="my-auto mx-auto">
            <a-button v-if="type == 'file' && (file as File).size" class="text-xl text-center" :to="`${modUrl}/download/${file.id}`" icon="mdi:download">
                {{$t('download')}}
                <small class="mt-2 text-center block">{{(file as File).type}} - {{friendlySize((file as File).size)}}</small>
            </a-button>
            <VDropdown v-else>
                <a-button class="text-xl text-center" icon="mdi:download">
                    {{$t('show_download_link')}}
                </a-button>
                <template #popper>
                    <div class="word-break p-2" style="width: 250px;">
                        {{$t('show_download_link_warn')}}
                        <br>
                        <a class="text-lg font-bold" :href="(file as any).url">{{(file as any).url}}</a>
                    </div>
                </template>
            </VDropdown>
        </div>
    </flex>
</template>

<script setup lang="ts">
import { File, Link, Mod } from '~~/types/models';

const props = defineProps<{
    file: File & Link,
    type: 'file'|'link',
    mod: Mod
}>();

const modUrl = computed(() => `/mod/${props.mod.id}`);

const image = computed(() => props.mod.images?.find(image => image.id == props.file.image_id));
</script>