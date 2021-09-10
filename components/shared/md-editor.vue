<template>
    <div class="md-editor p-2">
        <tabs padding="0" :hash="false">
            <tab name="write" title="Write">
                <textarea ref="textarea" class="textarea" :id="labelId" :value="value" @input="$emit('input', $event.target.value)" :rows="rows"/>
            </tab>
            <tab name="preview" title="Preview">
                <div v-html="mdHTML"/>
            </tab>
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
        </tabs>
    </div>
</template>

<script>
    import { parseMarkdown } from '../../utils/md-parser';

    export default {
        data: () => ({
            test: 'lol',
            toolGroups: [ //$ = selection
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
            ]
        }),
        props: {
            labelId: String,
            value: String,
            rows: String
        },
        computed: {
            mdHTML() {
                return parseMarkdown(this.value);
            },
        },
        methods: {
            clickedTool(tool) {
                const textarea = this.$refs.textarea;
                //textarea.focus(); //Force focus
                const [start, end] = [textarea.selectionStart, textarea.selectionEnd];
                let insert = tool.insert;
                let focus = start + insert.indexOf('$');
                textarea.setRangeText(insert.replace('$', ''), start, end, 'select');
                textarea.focus();
                textarea.setSelectionRange(focus, focus);
                this.$emit('input', textarea.value);
            }
        }
    };
</script>

<style scoped>
    .preview {
        background: var(--secondary-bg-color);
    }
    .textarea {
        resize : vertical;
        width: 100%;
    }

    .md-editor {
        background-color: #22262a;
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