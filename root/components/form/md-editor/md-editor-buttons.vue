<template>
    <flex wrap class="my-auto items-center text-xl">
        <template v-for="[i, group] of toolGroups.entries()">
            <a-button v-for="tool of group.tools" :key="tool.icon" :icon="tool.icon" color="none" @click="$emit('clickTool', tool)"/>
            <span v-if="i != toolGroups.length - 1" :key="group.name" class="tools-splitter"/>
        </template>
        <span class="tools-splitter"/>
        <a-button color="none" :icon="splitMode ? 'material-symbols:rectangle-rounded' : 'material-symbols:splitscreen-left'" @click="splitModeVm = !splitModeVm"/>
        <a-button color="none" :icon="fullscreen ? 'mdi:fullscreen-exit' : 'mdi:fullscreen'" @click="fullscreenVm = !fullscreenVm"/>
    </flex>
</template>

<script setup lang="ts">
// The $ sign tells it where to put the cursor and text if selected.
interface Tool {
    icon: string,
    insert: string
}

const fullscreen = ref(false);

const props = withDefaults(defineProps<{
    fullscreen: boolean,
    splitMode: boolean
}>(), {
    fullscreen: false,
    splitMode: false
});

const emit = defineEmits<{
    ( e: 'update:fullscreen', state: boolean ),
    ( e: 'update:splitMode', state: boolean ),
    ( e: 'clickTool', tool: Tool ),
}>();

const fullscreenVm = useVModel(props, 'fullscreen', emit);
const splitModeVm = useVModel(props, 'splitMode', emit);

const toolGroups = [ 
    {
        name: 'main',
        tools: [
            {icon: 'mdi:format-bold', insert: '**$**'},
            {icon: 'mdi:format-italic', insert: '*$*'},
            {icon: 'mdi:format-strikethrough', insert: '~~$~~'},
            {icon: 'mdi:format-underline', insert: '__$__'},
            {icon: 'mdi:format-header-pound', insert: '# $'},
            {icon: 'mdi:format-align-center', insert: ':::$:::'},
        ]
    },
    {
        name: 'fuck',
        tools: [
            {icon: 'mdi:format-quote-open', insert: '> $'},
            {icon: 'mdi:code-braces', insert: '```\n$\n```'},
            {icon: 'mdi:format-list-bulleted', insert: '* $'},
            {icon: 'mdi:format-list-numbered', insert: '1. $'},
            {icon: 'mdi:eye-off', insert: '||$||'}, //Spoiler
        ]
    },
    {
        name: 'media',
        tools: [
            {icon: 'mdi:link-variant', insert: '[$](https://)'},
            {icon: 'mdi:multimedia', insert: '![](https://$)'},
            {icon: 'ic:baseline-horizontal-rule', insert: '$\n\n-----'},
            {icon: 'mdi:table', insert: '| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| $     | Text     | Text     |'},
        ]
    }
];
</script>

<style scoped>
.tools-splitter {
    width: 2px;
    height: 16px;
    opacity: 0.5;
    vertical-align: middle;
    background: var(--secondary-text-color);
}
</style>