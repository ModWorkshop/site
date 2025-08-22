import MarkdownIt from 'markdown-it';

import DOMPurify from 'isomorphic-dompurify';
import parseBBCode from './bbcode-parser';
import markdownItRegex from '@gerhobbelt/markdown-it-regexp';
import taskLists from 'markdown-it-task-lists';

import hljs from 'highlight.js/lib/core';
import javascript from 'highlight.js/lib/languages/javascript';
import lua from 'highlight.js/lib/languages/lua';
import csharp from 'highlight.js/lib/languages/csharp';
import c from 'highlight.js/lib/languages/c';
import xml from 'highlight.js/lib/languages/xml';
import yaml from 'highlight.js/lib/languages/yaml';
import json from 'highlight.js/lib/languages/json';
import cpp from 'highlight.js/lib/languages/cpp';
import rust from 'highlight.js/lib/languages/rust';
import java from 'highlight.js/lib/languages/java';
import actionscript from 'highlight.js/lib/languages/actionscript';
import python from 'highlight.js/lib/languages/python';
import css from 'highlight.js/lib/languages/css';
import ini from 'highlight.js/lib/languages/ini';
import gradle from 'highlight.js/lib/languages/gradle';
import autohotkey from 'highlight.js/lib/languages/autohotkey';
import haxe from 'highlight.js/lib/languages/haxe';
import mention from './markdown/mention';
import { html5Media } from './markdown/media';

hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('lua', lua);
hljs.registerLanguage('csharp', csharp);
hljs.registerLanguage('c', c);
hljs.registerLanguage('xml', xml);
hljs.registerLanguage('yaml', yaml);
hljs.registerLanguage('json', json);
hljs.registerLanguage('json', json);
hljs.registerLanguage('cpp', cpp);
hljs.registerLanguage('rust', rust);
hljs.registerLanguage('java', java);
hljs.registerLanguage('actionscript', actionscript);
hljs.registerLanguage('python', python);
hljs.registerLanguage('css', css);
hljs.registerLanguage('ini', ini);
hljs.registerLanguage('gradle', gradle);
hljs.registerLanguage('autohotkey', autohotkey);
hljs.registerLanguage('haxe', haxe);

const md = MarkdownIt({
	html: true,
	breaks: true,
	linkify: true,
	mentions: true,
	highlight(str: string, lang: string) {
		try {
			let hl;
			if (lang && hljs.getLanguage(lang)) {
				hl = hljs.highlight(str, { language: lang }).value;
			} else {
				hl = hljs.highlightAuto(str).value;
			}
			return `<pre><code class="hljs">${hl}</code></pre>`;
		} catch (e) {
			console.error(e);
		}

		return '';
	}
});

/**
 * Makes __Text__ into underline instead of bold
 * Why? There's already a way to write bold text and it is the most popular - **Bold!**
 * In Discord which is a popular social media, people use __Underlne__ and it just makes sense.
 */
md.renderer.rules.strong_close = md.renderer.rules.strong_open = function (tokens, idx, opts, _, slf) {
	const token = tokens[idx];
	if (token.markup === '__') {
		token.tag = 'u';
	}
	return slf.renderToken(tokens, idx, opts);
};

md.use(html5Media);
md.use(taskLists);
md.use(markdownItRegex(
	/(?:^|\n)(?: {0,3})(:::+)(?: *)([\s\S]*?)\n?(?: {0,3})\1/,
	function ([, , match]) {
		return `\n\n<span class="center">${md.renderInline(match)}</span>\n\n`;
	}
));

md.use(mention);

export function oldParseMarkdown(text: string) {
	if (!text) {
		return '';
	}

	text = md.utils.escapeHtml(text); // First escape the ugly shit
	text = parseBBCode(text); // Handle BBCode
	text = text.replace(/&gt;/g, '>');
	text = text.replace(/&quot;/g, '"');

	text = text.replace(/(?:^|\n)(?: {0,3})(\|\|+)(?: *)([\s\S]*?)\n?(?: {0,3})\1/g, function (wholeStr, delimiter, match) {
		match = md.render(match);
		return `\n\n<div class="spoiler"><details><summary>Spoiler!</summary>${match}</details></div>\n\n`;
	});

	text = md.render(text); // Parse using markdown it
	return DOMPurify.sanitize(text, { // Finally, DOMPurify it!
		ADD_TAGS: ['iframe'],
		ADD_ATTR: ['frameborder', 'allow', 'allowfullscreen']
	});
}
