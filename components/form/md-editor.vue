<template>
    <a-input>
        <a-tabs :class="classes" style="flex:1;">
            <a-tab name="write" :title="$t('write_tab')" style="flex:1;">
                <textarea
                    :id="labelId"
                    ref="textArea"
                    v-model="vm"
                    type="textarea" 
                    :class="{textarea: true, 'input-error': !!err}"
                    :rows="rows" 
                    v-bind="$attrs" 
                />
            </a-tab>
            <a-tab name="preview" :title="$t('preview_tab')" class="preview p-2" :style="{'height': previewHeight}">
                <a-markdown class="h-100" :text="vm"/>
            </a-tab>
            <template #buttons>
                <flex wrap class="my-auto items-center text-xl">
                    <template v-for="[i, group] of toolGroups.entries()">
                        <a-button v-for="tool of group.tools" :key="tool.icon" :icon="tool.icon" color="none" @click="clickedTool(tool)"/>
                        <span v-if="i != toolGroups.length - 1" :key="group.name" class="tools-splitter"/>
                    </template>
                    <span class="tools-splitter"/>
                    <a-button color="none" :icon="fullscreen ? 'mdi:fullscreen-exit' : 'mdi:fullscreen'" @click="toggleFullscreen"/>
                </flex>
            </template>
        </a-tabs>
        <span v-if="err" class="text-danger">{{err}}</span>
    </a-input>
</template>

<script setup lang="ts">
const props = defineProps({
    labelId: String,
    modelValue: String,
    rows: { type: [String, Number], default: 12 }
});

const emit = defineEmits(['update:modelValue', 'textarea-keyup']);
const vm = useVModel(props, 'modelValue', emit);

const classes = computed(() => ({
    'md-editor': true,
    'p-2': true,
    'md-editor-fullscreen': fullscreen.value
}));

const fullscreen = ref(false);
const textArea = ref<HTMLTextAreaElement>();
const previewHeight = ref('0');

// The $ sign tells it where to put the cursor and text if selected.
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

const err = useWatchValidation(vm, textArea);

onMounted(() => {
    const textarea = textArea.value;
    if (textarea) {
        new ResizeObserver(() => {
            const textarea = textArea.value;
            if (textarea && textarea.parentElement!.style.display != 'none') {
                previewHeight.value = textarea.clientHeight + 3 + 'px';
            }
        }).observe(textarea);
    }
});

interface Tool {
    icon: string,
    insert: string
}

function clickedTool(tool: Tool) {
    const textarea = textArea.value;
    if (textarea) {
        textarea.focus(); //Force focus
        const [start, end] = [textarea.selectionStart, textarea.selectionEnd];
        const selectedText = textarea.value?.substring(start, end) ?? '';
        let insert = tool.insert;
        let focus = start + insert.indexOf('$');
        const inserted = insert.replace('$', selectedText);
        // textarea.setRangeText(inserted, start, end, 'select');
        document.execCommand("insertText", false, inserted); //If it's deprecated, then what the fuck am I supposed to use?
        emit('update:modelValue', textarea.value);
        textarea.focus();
        textarea.setSelectionRange(focus, focus);
    }
}

function toggleFullscreen() {
    fullscreen.value = !fullscreen.value;
    if (fullscreen.value) {
        document.body.classList.add('md-editor-open');
    } else {
        document.body.classList.remove('md-editor-open');
    }
    const textarea = textArea.value;
    if (textarea) {
        previewHeight.value = textarea.scrollHeight + 3 + 'px';
    }
}
</script>

<style>
.tab-panel, .tab-panels {
    height: 100%;
}
</style>

<style scoped>
.preview {
    display: flex;
    flex-direction: column;
    background: var(--content-bg-color);
    overflow-y: scroll;
    resize : vertical;
}

.md-editor-fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100% !important;
    z-index: 9999;
}

.textarea {
    background-color: var(--input-bg-color);
    border-radius: var(--border-radius);
    resize : vertical;
    width: 100%;
}

.md-editor-fullscreen .textarea {
    height: 100% !important;
    resize: none;
}

.md-editor-fullscreen .preview {
    resize: none;
    height: 100% !important;
}

.md-editor {
    background-color: var(--alt-content-bg-color);
    border-radius: var(--border-radius);
    resize: vertical;
}

textarea {
    height: 100%;
}

textarea:focus-visible {
    outline-color: #107ef4;
    outline-style: groove;
}

.md-tool {
    background: none;
    padding: 0.5rem;
}

.tools-splitter {
    width: 2px;
    height: 16px;
    opacity: 0.5;
    vertical-align: middle;
    background: var(--secondary-text-color);
}
</style>