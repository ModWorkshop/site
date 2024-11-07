<template>
    <m-input>
        <m-tabs :class="classes" :style="{ height }">
            <m-tab v-if="!splitMode" name="write" :title="$t('write_tab')">
                <md-editor-textarea ref="textAreaComp" v-model="vm" :label-id="labelId" :rows="rows" v-bind="$attrs" @keydown="onKeyDown"/>
            </m-tab>
            <m-tab v-if="!splitMode" name="preview" :title="$t('preview_tab')" class="preview p-2" >
                <md-content :text="vm"/>
            </m-tab>
            <m-tab v-else name="split-mode" :title="$t('split_mode_tab')">
                <m-flex class="overflow-hidden h-full">
                    <md-editor-textarea ref="textAreaComp" v-model="vm" :label-id="labelId" :rows="rows" style="flex:1;" @keydown="onKeyDown"/>
                    <md-content ref="mdText" class="preview" :text="vm"/>
                </m-flex>
            </m-tab>

            <template #buttons>
                <md-editor-buttons v-model:fullscreen="fullscreen" v-model:split-mode="splitMode" @click-tool="clickTool"/>
            </template>
        </m-tabs>
        <span v-if="err" class="text-danger">{{err}}</span>
        <template #desc>
            <slot name="desc"/>
        </template>
    </m-input>
</template>

<script setup lang="ts">
import type { Tool } from '~/types/tools';

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
    'fullscreen': fullscreen.value,
    'split': splitMode.value,
}));

const textAreaComp = ref();
const mdText = ref();
const textArea = computed<HTMLTextAreaElement>(() => textAreaComp.value?.element);
const err = useWatchValidation(vm, textArea);
const height = ref<string>((parseInt(props.rows as string) * 26) + 'px');
provide('err', err);

function clickTool(tool: Tool) {
    const textarea = textArea.value;
    
    if (textarea) {
        textarea.focus(); //Force focus
        const [start, end] = [textarea.selectionStart, textarea.selectionEnd];
        const selectedText: string = textarea.value?.substring(start, end) ?? '';
        const insert = tool.insert;
        
        let pushOffset = insert.indexOf('$');
        let focusStart = start + pushOffset;
        let focusEnd = end + pushOffset;

        let inserted;
        
        if (tool.multiline) {
            const lines = selectedText.split('\n');
            let pushOffset = insert.replace('$line', '1').indexOf('$');

            focusStart = start + pushOffset;
            focusEnd = end + pushOffset;

            inserted = '';
            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                if (i != 0) {
                    inserted += '\n';
                }

                const strWithLineNum = insert.replace('$line', (i + 1).toString());
                inserted += strWithLineNum.replace('$', line);

                focusEnd += strWithLineNum.indexOf('$');
            }

            focusEnd--;
        } else{
            inserted = insert.replace('$', selectedText);
        }

        // textarea.setRangeText(inserted, start, end, 'select');
        document.execCommand("insertText", false, inserted); //If it's deprecated, then what the fuck am I supposed to use?
        emit('update:modelValue', textarea.value);
        textarea.focus();
        textarea.setSelectionRange(focusStart, focusEnd);
    }
}

function onKeyDown(e: KeyboardEvent) {
    if (e.key == 'Tab') {
        e.preventDefault();
        clickTool({
            insert: '\t$',
            multiline: true
        });
    }
}

watch(fullscreen, status => {
    if (status) {
        document.body.classList.add('md-editor-open');
    } else {
        document.body.classList.remove('md-editor-open');
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
    background: var(--content-bg-color);
    overflow-y: scroll;
    flex: 1;
}

.md-editor.fullscreen {
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
    resize : none;
    height: 100%;
    width: 100%;
}

.md-editor .textarea:focus-visible {
    outline: none;
}

.md-editor.fullscreen {
    height: 100% !important;
    max-height: initial;
    resize: none;
}

.md-editor {
    background-color: var(--alt-content-bg-color);
    border-radius: var(--border-radius);
    resize: vertical;
    overflow-y: hidden;
    min-height: 180px;
    max-height: 90vh;
    max-width: 100%;
}
</style>