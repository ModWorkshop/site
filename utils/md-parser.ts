import MarkdownIt  from 'markdown-it';
import hljs from 'highlight.js';
import DOMPurify from 'isomorphic-dompurify';
import { escapeHtml } from 'markdown-it/lib/common/utils.js';
import parseBBCode from './bbcode-parser';

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
		}
		catch (e) {
			console.error(e);
		}

		return '';
	}
});


md.inline.ruler.after('emphasis', 'mention', function(state, silent) {
	let end = state.pos+1;
	const max = state.posMax, start = state.pos;	

	if (silent || state.src.charCodeAt(start) !== 64/* @ */ || state.src.charCodeAt(start-1) === 59) { 
		return false;
	}
	if (start + 2 >= max) { 
		return false;
	}

	state.pos = start + 1;
	
	while (end < max) {
		if (/([^a-zA-Z0-9_-]+)/g.test(state.src.charAt(end))) {
			break;
		}

		end++;
	}

	const content = (state.src as string).slice(start + 1, end);

	// don't allow unescaped spaces/newlines inside
	if (content.match(/(^|[^\\])(\\\\)*\s/)) {
		state.pos = start;
		return false;
	}

	// found!
	state.posMax = end - 1;
	state.pos = start + 1;

	// Earlier we checked !silent, but this implementation does not need it
	let token = state.push('link_open', 'a', 1);
	token.markup  = '@';
	token.attrs = [['href', `/@${content}`]];

	token = state.push('text', '', 0);
	token.content = '@'+content;

	token = state.push('link_close', 'a', -1);
	token.markup  = '@';

	state.pos = state.posMax + 1;
	state.posMax = max;
	return true;
});

export function parseMarkdown(text: string) {
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