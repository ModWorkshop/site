<template>
    <a-input v-model="modelValue">
        <a-tabs :class="classes">
            <a-tab name="write" title="Write">
                <textarea
                    :id="labelId"
                    ref="textArea"
                    v-model="modelValue"
                    type="textarea" 
                    :class="{textarea: true, 'input-error': !!err}"
                    v-bind="$attrs" 
                    :rows="rows" @input="$emit('update:modelValue', modelValue)"
                />
            </a-tab>
            <a-tab name="preview" title="Preview" class="preview p-2" :style="{'height': previewHeight}">
                <a-markdown class="h-100" :text="modelValue"/>
            </a-tab>
            <template #buttons>
                <flex class="ml-auto my-auto items-center">
                    <template v-for="[i, group] of toolGroups.entries()">
                        <a-button v-for="tool of group.tools" :key="tool.icon" :icon="tool.icon" color="none" @click="clickedTool(tool)"/>
                        <span v-if="i != toolGroups.length - 1" :key="group.name" class="tools-splitter"/>
                    </template>
                    <span class="tools-splitter"/>
                    <a-button color="none" :icon="fullscreen ? 'xmark' : 'arrows-up-down-left-right'" @click="toggleFullscreen"/>
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

const classes = computed(() => ({
    'md-editor': true,
    'p-2': true,
    'md-editor-fullscreen': fullscreen.value
}));

const fullscreen = ref(false);
const textArea = ref<HTMLTextAreaElement>();
const previewHeight = ref('0');
const toolGroups = [ 
    {
        name: 'main',
        tools: [
            {icon: 'bold', insert: '**$**'},
            {icon: 'italic', insert: '*$*'},
            {icon: 'strikethrough', insert: '~~$~~'},
            {icon: 'underline', insert: '__$__'},
            {icon: 'heading', insert: '# $'},
            {icon: 'align-center', insert: ':::$:::'},
        ]
    },
    {
        name: 'fuck',
        tools: [
            {icon: 'quote-left', insert: '> $'},
            {icon: 'code', insert: '```\n$\n```'},
            {icon: 'list-ul', insert: '* $'},
            {icon: 'list-ol', insert: '1. $'},
            {icon: 'eye-slash', insert: '||$||'}, //Spoiler
        ]
    },
    {
        name: 'media',
        tools: [
            {icon: 'link', insert: '[$](https://)'},
            {icon: 'photo-video', insert: '![](https://$)'},
            {icon: 'minus', insert: '$\n\n-----'},
            {icon: 'table', insert: '| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| Text     | Text     | Text     |'},
        ]
    }
];

const vm = toRef(props, 'modelValue');
const emit = defineEmits(['update:modelValue', 'textarea-keyup']);
const err = useWatchValidation(vm, textArea);

onMounted(() => {
    const textarea = textArea.value;
    new ResizeObserver(() => {
        const textarea = textArea.value;
        if (textarea.parentElement.style.display != 'none') {
            previewHeight.value = textarea.clientHeight + 3 + 'px';
        }
    }).observe(textarea);
});

interface Tool {
    icon: string,
    insert: string
}

function clickedTool(tool: Tool) {
    const textarea = textArea.value;
    //textarea.focus(); //Force focus
    const [start, end] = [textarea.selectionStart, textarea.selectionEnd];
    let insert = tool.insert;
    let focus = start + insert.indexOf('$');
    textarea.setRangeText(insert.replace('$', ''), start, end, 'select');
    emit('update:modelValue', textarea.value);
    textarea.focus();
    textarea.setSelectionRange(focus, focus);
}

function toggleFullscreen() {
    fullscreen.value = !fullscreen.value;
    if (fullscreen.value) {
        document.body.classList.add('md-editor-open');
    } else {
        document.body.classList.remove('md-editor-open');
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
    resize: none;
    height: 100% !important;
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