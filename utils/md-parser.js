import MarkdownIt  from 'markdown-it';
import hljs from 'highlight.js';
import DOMPurify from 'isomorphic-dompurify';
import { escapeHtml } from 'markdown-it/lib/common/utils';
import parseBBCode from './bbcode-parser';

const md = MarkdownIt({
	html: true,
	breaks: true,
	linkify: true,
	highlight(str, lang) {
		try {
			let hl;
			if (lang && hljs.getLanguage(lang)) {
				hl = hljs.highlight(str, { language: lang }).value;
			} else {
				hl = hljs.highlightAuto(str).value;
			}
			return `<pre><code class="hljs">${hl}</code></pre>`;
		}
		catch (e) {
			console.error(e);
		}

		return '';
	}
});

export function parseMarkdown(text) {
	if (!text) {
		return '';
	}
    //TODO: consider disabling BBCode for new/updated mods
	text = escapeHtml(text); //First escape the ugly shit
    text = parseBBCode(text); //Handle BBCode
    text = md.render(text); //Parse using markdown it
    return DOMPurify.sanitize(text, { //Finally, DOMPurify it!
        ADD_TAGS: ['iframe'],
        ADD_ATTR: ['frameborder', 'allow', 'allowfullscreen'],
    });
}