<template>
    <div class="md-editor p-2">
        <a-tabs padding="0" type="none">
            <a-tab name="write" title="Write">
                <textarea type="textarea" ref="textArea" class="textarea" :id="labelId" v-model="modelValue" @change="$emit('update:modelValue', modelValue)" :rows="rows"/>
            </a-tab>
            <a-tab name="preview" title="Preview" :style="{'min-height': previewHeight}">
                <markdown :text="modelValue"/>
            </a-tab>
            <template v-slot:buttons>
                <flex gap="1" class="ml-auto my-auto items-center">
                    <template v-for="[i, group] of toolGroups.entries()">
                        <button v-for="tool of group.tools" class="md-tool" @click="clickedTool(tool)" :key="tool.icon">
                            <font-awesome-icon :icon="tool.icon"/>
                        </button>
                        <span class="tools-splitter" :key="group.name" v-if="i != toolGroups.length - 1"/>
                    </template>
                </flex>
            </template>
        </a-tabs>
    </div>
</template>

<script setup>
const textArea = ref([]);
const previewHeight = ref(0);
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
            {icon: 'photo-video', insert: '[](https://$)'},
            {icon: 'minus', insert: '$\n\n-----'},
            {icon: 'table', insert: '| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| Text     | Text     | Text     |'},
        ]
    }
];

defineProps({
    labelId: String,
    modelValue: String,
    rows: String
});

const emit = defineEmits(['update:modelValue']);

onMounted(() => {
    const textarea = textArea.value;
    new ResizeObserver(() => {
        if (textarea.parentElement.style.display != 'none') {
            previewHeight.value = textarea.clientHeight + 2 + 'px';
        }
    }).observe(textarea);
});

function clickedTool(tool) {
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
</script>

<style scoped>
    .preview {
        background: var(--secondary-bg-color);
    }
    .textarea {
        color: var(--text-color);
        background-color: var(--input-bg-color);
        resize : vertical;
        width: 100%;
    }

    .md-editor {
        background-color: #22262a;
    }

    textarea:focus-visible {
        outline-color: #107ef4;
        outline-style: groove;
    }

    .md-tool {
        background: none;
        color: var(--text-color);
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