import MarkdownIt  from 'markdown-it';

import DOMPurify from 'isomorphic-dompurify';
import { escapeHtml } from 'markdown-it/lib/common/utils.js';
import parseBBCode from './bbcode-parser';
import markdownItRegex from '@gerhobbelt/markdown-it-regexp';
import { html5Media } from 'markdown-it-html5-media';
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

md.use(html5Media);

md.use(taskLists, {enabled: true});

md.use(markdownItRegex(
	/(?:^|\n)(?: {0,3})(:::+)(?: *)([\s\S]*?)\n?(?: {0,3})\1/,
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

	while (end < max) {
		if (/([^a-zA-Z0-9_-]+)/g.test(state.src.charAt(end))) {
			break;
		}

		end++;
	}

	// Avoid highlighting lone @'s
	if (end-start == 1) {
		return false;
	}
	
	state.pos = start + 1;

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

const fullYoutubeRegex = /(?:(?:https?:)?(?:\/\/)?)(?:(?:www)?\.)?(?:youtube|youtu)\.(?:com|be)\/(?:(?:watch\?v=)|(?:embed\/)?)([a-zA-Z0-9_-]{11})(?:\?t=(\d+))?/i;
const shortYoutubeRegex = /(?:(?:https?:)?(?:\/\/)?)?youtu\.be\/([a-zA-Z0-9_-]{11})(?:\?t=(\d+))?/i;
const vimeoRegex = /(?:(?:https?:)?(?:\/\/)?)(?:(?:www)?\.)?vimeo.com\/(\d+)/i;
const gyfcatRegex = /(?:(?:https?:)?(?:\/\/)?)(?:(?:www)?\.)?gfycat.com\/([a-zA-Z]+)/i;
const streamableRegex = /https:\/\/streamable.com(?:\/\w+)?\/(\w+)/i;
const soundcloudRegex = /https?:\/\/(?:www.)?soundcloud.com\/([\w-]+\/[\w-]+)/i;
const inlineRegExp = /!\[([^\]]*?)][ \t]*()\([ \t]?<?([\S]+?(?:\([\S]*?\)[\S]*?)?)>?(?: =([*\d]+[A-Za-z%]{0,4})x([*\d]+[A-Za-z%]{0,4}))?[ \t]*(?:(["'])([^"]*?)\6)?[ \t]?\)/g;

function makeIFrame(src: string, w = 560, h = 315) {
	return `<iframe 
		width="${w}"
		height="${h}"
		src="${src}"
		frameborder="0"
		allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
		allowfullscreen
	/></iframe>`;
}

function parseMedia(wholeMatch, altText, linkId, url) {
	const youtubeData = shortYoutubeRegex.exec(url) || fullYoutubeRegex.exec(url);
	if (youtubeData) {
		return makeIFrame(`https://www.youtube.com/embed/${youtubeData[1]}?rel=0${youtubeData[2] ? '&t='+youtubeData[2] : ''}`);
	}

	const streamable = streamableRegex.exec(url);
	if (streamable) {
		return makeIFrame(`https://streamable.com/s/${streamable[1]}`);
	}

	const vimeo = vimeoRegex.exec(url);
	if (vimeo) {
		return makeIFrame(`https://player.vimeo.com/video/${vimeoRegex[1]}`, 640, 266);
	}

	const gyfyCat = gyfcatRegex.exec(url);
	if (gyfyCat) {
		return makeIFrame(`https://gfycat.com/ifr/${gyfyCat[1]}`);
	}

	const soundCloud = soundcloudRegex.exec(url);
	if (soundCloud) {
		return makeIFrame(`https://w.soundcloud.com/player/?url=https://soundcloud.com/${soundCloud[1]}`, 560, 166);
	}

	return wholeMatch;
}

export function parseMarkdown(text: string) {
	if (!text) {
		return '';
	}

    //TODO: consider disabling BBCode for new/updated mods
	text = escapeHtml(text); //First escape the ugly shit
    text = parseBBCode(text); //Handle BBCode

	text = text.replace(/&gt;/g, '>');
	text = text.replace(/&quot;/g, '"');

	text = text.replace(/(?:^|\n)(?: {0,3})(\|\|+)(?: *)([\s\S]*?)\n?(?: {0,3})\1/g, function(wholeStr, delimiter, match) {		
		match = md.render(match);
		return `\n\n<div class="spoiler"><details><summary>Spoiler!</summary><div>${match}</details></div>\n\n`;
	});
	text = text.replace(inlineRegExp, parseMedia);
    text = md.render(text); //Parse using markdown it
    return DOMPurify.sanitize(text, { //Finally, DOMPurify it!
        ADD_TAGS: ['iframe'],
        ADD_ATTR: ['frameborder', 'allow', 'allowfullscreen'],
    });
}