<template>
    <flex column class="items-center text-center">
        <h2>{{$t('downloading_file')}}</h2>
        <h3>{{file.type}} - {{friendlySize(file.size)}}</h3>
        <h3>{{$t('downloading_file_should')}}</h3>
        <flex>
            <a-button icon="arrow-left" :to="`/mod/${mod.id}`">{{$t('back_to_mod')}}</a-button>
            <a ref="download" download :href="downloadUrl">
                <a-button icon="download">{{$t('force_download')}}</a-button>
            </a>
            <a-button 
                v-if="props.mod.instructs_template || props.mod.instructions" 
                :to="`/mod/${mod.id}?tab=instructions`"
                icon="circle-question"
            >
            {{$t('downloading_file_help')}}
        </a-button>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { Mod } from '~~/types/models';
import { friendlySize } from '~~/utils/helpers';

const { public: config } = useRuntimeConfig();

const route = useRoute();

const props = defineProps<{
    mod: Mod
}>();

const download = ref(null);

const downloadUrl = computed(() => `${config.apiUrl}/files/${file.value.id}/download`);
const file = computed(() => props.mod.files.find(file => file.id == parseInt((route.params.fileId as string))));

if (!file.value) {
    throw createError({ statusCode: 404, statusMessage: "File doesn't exist!"});
}

//Annoyingly we needed to wrap the button in a different anchor element since ref doesn't always include the element in the DOM
//Basically if it's a componnent, it will be a component ref which doesn't seem to include the actual element!
//Otherwise if it's a simple element, it will be the element itself.
watch(download, () => {
    if (download.value) {
        download.value.click();
        registerDownload(props.mod);
    }
});
</script>