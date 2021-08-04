<template>
	<div>
		Preview:
		<div v-html="md"/>
		<el-input v-model="mdText" type="textarea" rows="6"/>
		
		<br>
		<a href="https://modworkshop.net">Ok</a>
		<br>
		<br>

		
		<el-button type="primary">Hello!</el-button>

		<h1>Theme: {{ $colorMode.value }}</h1>
		<el-select v-model="$colorMode.preference">
			<el-option value="system">System</el-option>
			<el-option value="light">Light</el-option>
			<el-option value="dark">Dark</el-option>
		</el-select>
	</div>
</template>

<script>
import showdown from 'showdown';
import BBCodeParser from '../utils/bbcode-parser';
showdown.setFlavor('github');
const converter = new showdown.Converter({
	parseImgDimensions: true,
	//extensions: ['code-highlight', 'discord-spoiler', 'youtube', 'header-anchors'],
	underline: true,
	ghMentions: false,
	simplifiedAutoLink: true,
	ghMentionsLink: '/user/{u}'
});

export default {
	data() {
		return {
			mdText: '**hello**',
		}
	},
	computed: {
		md() {
			let text = BBCodeParser.process(this.mdText);
			text = text.replace(/&gt;/g, '>');
     		text = text.replace(/&quot;/g, '"');
        	text = converter.makeHtml(text);

			return text;
		}
	}
}
</script>
