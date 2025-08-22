<template>
	<m-flex class="overflow-x-auto overflow-y-hidden items-center">
		<m-flex class="items-center text-lg flex-1 flex-shrink-0 p-1" gap="1">
			<template v-for="[i, group] of toolGroups.entries()">
				<m-button v-for="tool of group.tools" :key="tool.insert" :icon="tool.icon" color="none" @click="$emit('clickTool', tool)"/>
				<span v-if="i != toolGroups.length - 1" :key="group.name" class="tools-splitter"/>
			</template>
			<span class="tools-splitter"/>
			<m-button color="none" :icon="splitMode ? MdiRectangle : MdiViewSplitVertical" @click="splitModeVm = !splitModeVm"/>
			<m-button color="none" :icon="fullscreen ? IMdiFullScreenExit : IMdiFullScreen" @click="fullscreenVm = !fullscreenVm"/>
		</m-flex>
	</m-flex>
</template>

<script setup lang="ts">
import IMdiFormatBold from '~icons/mdi/format-bold';
import IMdiFormatItalic from '~icons/mdi/format-italic';
import IMdiFormatStrikeThrough from '~icons/mdi/format-strikethrough';
import IMdiFormatUnderline from '~icons/mdi/format-underline';
import IMdiFormatHeaderPound from '~icons/mdi/format-header-pound';
import IMdiFormatAlignCenter from '~icons/mdi/format-align-center';
import IMdiFormatQuoteOpen from '~icons/mdi/format-quote-open';
import IMdiFormatCodeBraces from '~icons/mdi/code-braces';
import IMdiFormatEyeOff from '~icons/mdi/eye-off';
import IMdiFormatListNumbered from '~icons/mdi/format-list-numbered';
import IMdiFormatListBulleted from '~icons/mdi/format-list-bulleted';
import IMdiLinkVariant from '~icons/mdi/link-variant';
import IMdiMultimedia from '~icons/mdi/multimedia';
import HorizontalRule from '~icons/mdi/minus';
import IMdiTable from '~icons/mdi/table';
import IMdiFullScreenExit from '~icons/mdi/fullscreen-exit';
import IMdiFullScreen from '~icons/mdi/fullscreen';
import MdiViewSplitVertical from '~icons/mdi/view-split-vertical';
import MdiRectangle from '~icons/mdi/rectangle';
import MdiFormatColorFill from '~icons/mdi/format-color-fill';

import type { Tool } from '~/types/tools';

// The $ sign tells it where to put the cursor and text if selected.

const props = withDefaults(defineProps<{
	fullscreen?: boolean;
	splitMode?: boolean;
}>(), {
	fullscreen: false,
	splitMode: false
});

const emit = defineEmits<{
	(e: 'update:fullscreen', state: boolean);
	(e: 'update:splitMode', state: boolean);
	(e: 'clickTool', tool: Tool);
}>();

const fullscreenVm = useVModel(props, 'fullscreen', emit);
const splitModeVm = useVModel(props, 'splitMode', emit);

interface ToolGroup {
	name: string;
	tools: Tool[];
}

const toolGroups: ToolGroup[] = [
	{
		name: 'inline',
		tools: [
			{ icon: IMdiFormatBold, insert: '**$**' },
			{ icon: IMdiFormatItalic, insert: '*$*' },
			{ icon: IMdiFormatStrikeThrough, insert: '~~$~~' },
			{ icon: IMdiFormatUnderline, insert: '__$__' },
			{ icon: IMdiFormatHeaderPound, insert: '# $' },
			{ icon: IMdiFormatAlignCenter, insert: ':::$:::' },
			{ icon: MdiFormatColorFill, insert: '{#007bff}($)' }
		]
	},
	{
		name: 'block',
		tools: [
			{ icon: IMdiFormatQuoteOpen, insert: '> $' },
			{ icon: IMdiFormatCodeBraces, insert: '```\n$\n```' },
			{ icon: IMdiFormatListBulleted, insert: '* $', multiline: true },
			{ icon: IMdiFormatListNumbered, insert: '$line. $', multiline: true },
			{ icon: IMdiFormatEyeOff, insert: '!!!\n$\n!!!' } // Spoiler
		]
	},
	{
		name: 'media',
		tools: [
			{ icon: IMdiLinkVariant, insert: '[$](https://)' },
			{ icon: IMdiMultimedia, insert: '![](https://$)' },
			{ icon: HorizontalRule, insert: '$\n\n-----' },
			{ icon: IMdiTable, insert: '| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| $     | Text     | Text     |' }
		]
	}
];
</script>

<style scoped>
.tools-splitter {
	width: 2px;
	height: 16px;
	opacity: 0.5;
	background: var(--secondary-text-color);
}
</style>
