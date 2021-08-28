<template>
	<div class="content-block content-block-large">
		Preview:
		<div v-html="md"/>
		<br>
		<el-input v-model="mdText" type="textarea" rows="10"/>
		
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
import { parseMarkdown } from '../utils/md-parser';

export default {
	data() {
		return {
			mdText: '**hello**',
		}
	},
	computed: {
		md() {
			return parseMarkdown(this.mdText)
		}
	}
}
</script>
