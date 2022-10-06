import MarkdownIt  from 'markdown-it';
import hljs from 'highlight.js';
import DOMPurify from 'isomorphic-dompurify';
import { escapeHtml } from 'markdown-it/lib/common/utils.js';
import parseBBCode from './bbcode-parser';
import markdownItRegex from '@gerhobbelt/markdown-it-regexp';
import { html5Media } from 'markdown-it-html5-media';

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

function renderUnderline(tokens, idx, opts, _, slf) {
	const token = tokens[idx];
	
	if (token.markup === '__') {
		token.tag = 'u';
	}

	return slf.renderToken(tokens, idx, opts);
}

md.renderer.rules.strong_open = renderUnderline;
md.renderer.rules.strong_close = renderUnderline;

const fullYoutubeRegex = /(?:(?:https?:)?(?:\/\/)?)(?:(?:www)?\.)?(?:youtube|youtu)\.(?:com|be)\/(?:(?:watch\?v=)|(?:embed\/)?)([a-zA-Z0-9_-]{11})(?:\?t=(\d+))?/i;
const shortYoutubeRegex = /(?:(?:https?:)?(?:\/\/)?)?youtu\.be\/([a-zA-Z0-9_-]{11})(?:\?t=(\d+))?/i;
const vimeoRegex = /(?:(?:https?:)?(?:\/\/)?)(?:(?:www)?\.)?vimeo.com\/(\d+)/;
const gyfcatRegex = /(?:(?:https?:)?(?:\/\/)?)(?:(?:www)?\.)?gfycat.com\/([a-zA-Z]+)/;
const streamableRegex = /https:\/\/streamable.com(?:\/\w+)?\/(\w+)/;
const inlineRegExp = /!\[([^\]]*?)][ \t]*()\([ \t]?<?([\S]+?(?:\([\S]*?\)[\S]*?)?)>?(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*(?:(["'])([^"]*?)\6)?[ \t]?\)/g;

md.use(html5Media);

md.use(markdownItRegex(
	/(?:^|\n)(?: {0,3})(:::+)(?: *)([\s\S]*?)\n?(?: {0,3})\1/g,
	function([, , match]) {
		match = md.render(match);
		return `\n\n<div class="center">${match}</div>\n\n`;
	}
));

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
	token.attrs = [['href', `/user/${content}`]];

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
	text = text.replace(/(?:^|\n)(?: {0,3})(\|\|+)(?: *)([\s\S]*?)\n?(?: {0,3})\1/g, function(wholeStr, delimiter, match) {		
		match = md.render(match);
		return `\n\n<div><details><summary>Spoiler!</summary>${match}</details></div>\n\n`;
	});
	text = text.replace(inlineRegExp, function(wholeMatch, altText, linkId, url, width, height, m5, title) {
		let m, w = 560, h = 315, src = '';
		if ((m = shortYoutubeRegex.exec(url)) || (m = fullYoutubeRegex.exec(url))) {
			src = 'https://www.youtube.com/embed/' + m[1] + '?rel=0';
			if (m[2])
				src += '&t='+m[2];
			// if (options.youtubejsapi)
			// 	src += '&enablejsapi=1';
		}
		else if(m = streamableRegex.exec(url)) {
			src = 'https://streamable.com/s/' + m[1];
		}
		else if (m = vimeoRegex.exec(url)) {
			w = 640;
			h = 266;
			src = 'https://player.vimeo.com/video/' + m[1];
		}
		else if(m = gyfcatRegex.exec(url)) {
			src = 'https://gfycat.com/ifr/' + m[1];
		}
		else {
			return wholeMatch;
		}

		return `<iframe width="${w}" height="${h}" src="${src}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
	});
    text = md.render(text); //Parse using markdown it
    return DOMPurify.sanitize(text, { //Finally, DOMPurify it!
        ADD_TAGS: ['iframe'],
        ADD_ATTR: ['frameborder', 'allow', 'allowfullscreen'],
    });
}