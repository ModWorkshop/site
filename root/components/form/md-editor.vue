<template>
    <a-input>
        <flex v-if="splitMode" column :class="classes" style="overflow:hidden;">
            <md-editor-buttons v-model:fullscreen="fullscreen" v-model:split-mode="splitMode" @click-tool="clickTool"/>
            <flex class="p-2 overflow-hidden h-full">
                <md-editor-textarea ref="textAreaComp" v-model="vm" :label-id="labelId" :rows="rows" style="flex:1;"/>
                <a-markdown ref="mdText" class="h-full preview" :style="{'height': previewHeight}" v-bind="$attrs"/>
            </flex>
        </flex>
        <a-tabs v-else :class="classes" style="flex:1;">
            <a-tab name="write" :title="$t('write_tab')" style="flex:1;">
                <md-editor-textarea ref="textAreaComp" v-model="vm" :label-id="labelId" :rows="rows" v-bind="$attrs"/>
            </a-tab>
            <a-tab name="preview" :title="$t('preview_tab')" class="preview p-2" :style="{'height': previewHeight}">
                <a-markdown class="h-100" :text="vm"/>
            </a-tab>
            <template #buttons>
                <md-editor-buttons v-model:fullscreen="fullscreen" v-model:split-mode="splitMode" @click-tool="clickTool"/>
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

const fullscreen = ref(false);
const splitMode = ref(false);

const classes = computed(() => ({
    'md-editor': true,
    'p-2': true,
    'md-editor-fullscreen': fullscreen.value
}));


const textAreaComp = ref();
const mdText = ref();
const textArea = computed(() => textAreaComp.value?.element);

const previewHeight = ref('0');



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

function clickTool(tool: Tool) {
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

watch(fullscreen, status => {
    if (status) {
        document.body.classList.add('md-editor-open');
    } else {
        document.body.classList.remove('md-editor-open');
    }
    const textarea = textArea.value;
    if (textarea) {
        previewHeight.value = textarea.scrollHeight + 3 + 'px';
    }
});
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
    flex: 1;
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
</style>