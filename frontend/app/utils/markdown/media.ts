import MarkdownIt, { type Options } from 'markdown-it';
import type Token from 'markdown-it/lib/token.mjs';

/**
 * A minimalist `markdown-it` plugin for parsing video/audio references inside
 * markdown image syntax as `<video>` / `<audio>` tags.
 *
 * https://github.com/eloquence/markdown-it-html5-media/blob/master/lib/index.js
 */

// We can only detect video/audio files from the extension in the URL.
// We ignore MP1 and MP2 (not in active use) and default to video for ambiguous
// extensions (MPG, MP4)
const validAudioExtensions = ['aac', 'm4a', 'mp3', 'oga', 'ogg', 'wav'];
const validVideoExtensions = ['mp4', 'm4v', 'ogv', 'webm', 'mpg', 'mpeg', 'avi'];

/**
 * @property {Object} messages
 * @property {Object} messages.languageCode
 *  a set of messages identified with a language code, typically an ISO639 code
 * @property {String} messages.languageCode.messageKey
 *  an individual translation of a message to that language, identified with a
 *  message key
 * @typedef {Object} MessagesObj
 */
let messages = {
  en: {
    'html5 video not supported': 'Your browser does not support playing HTML5 video.',
    'html5 audio not supported': 'Your browser does not support playing HTML5 audio.',
    'html5 media fallback link': 'You can <a href="%s" download>download the file</a> instead.',
    'html5 media description': 'Here is a description of the content: %s'
  }
};

/**
 * You can override this function using options.translateFn.
 *
 * @param language
 *  a language code, typically an ISO 639-[1-3] code.
 * @param messageKey
 *  an identifier for the message, typically a short descriptive text
 * @param messageParams
 *  Strings to be substituted into the message using some pattern, e.g., %s or
 *  %1$s, %2$s. By default we only use a simple %s pattern.
 * @returns
 *  the translation to use
 */
let translate = function(language: string, messageKey: string, messageParams?: string[]) {

	// Revert back to English default if no message object, or no translation
	// for this language
	if (!messages[language] || !messages[language][messageKey])
		language = 'en';

	if (!messages[language])
		return '';

	let message = messages[language][messageKey] || '';

	if (messageParams)
		for (const param of messageParams)
			message = message.replace('%s', param);

	return message;
};


/**
 * A fork of the built-in image tokenizer which guesses video/audio files based
 * on their extension, and tokenizes them accordingly.
 *
 * @param state
 *  Markdown-It state
 * @param silent
 *  if true, only validate, don't tokenize
 * @param md
 *  instance of Markdown-It used for utility functions
 */
function tokenizeImagesAndMedia(state: any, silent: boolean, md: MarkdownIt): boolean {
	let attrs, code, label, pos, ref, res, title, tokens, start;
	let href = '';
	const oldPos = state.pos, max = state.posMax;

	// Exclamation mark followed by open square bracket - ![ - otherwise abort
	if (state.src.charCodeAt(state.pos) !== 0x21 || state.src.charCodeAt(state.pos + 1) !== 0x5B)
		return false;

	const labelStart = state.pos + 2;
	const labelEnd = state.md.helpers.parseLinkLabel(state, state.pos + 1, false);

	// Parser failed to find ']', so it's not a valid link
	if (labelEnd < 0)
		return false;

	pos = labelEnd + 1;
	if (pos < max && state.src.charCodeAt(pos) === 0x28) { // Parenthesis: (
		//
		// Inline link
		//

		// [link](  <href>  "title"  )
		//        ^^ skipping these spaces
		pos++;
		for (; pos < max; pos++) {
			code = state.src.charCodeAt(pos);
			if (!md.utils.isSpace(code) && code !== 0x0A) // LF \n
				break;
		}
		if (pos >= max)
			return false;

		// [link](  <href>  "title"  )
		//          ^^^^^^ parsing link destination
		start = pos;
		res = state.md.helpers.parseLinkDestination(state.src, pos, state.posMax);
		if (res.ok) {
			href = state.md.normalizeLink(res.str);
			if (state.md.validateLink(href)) {
				pos = res.pos;
			} else {
				href = '';
			}
		}

		// [link](  <href>  "title"  )
		//                ^^ skipping these spaces
		start = pos;
		for (; pos < max; pos++) {
			code = state.src.charCodeAt(pos);
			if (!md.utils.isSpace(code) && code !== 0x0A)
				break;
		}

		// [link](  <href>  "title"  )
		//                  ^^^^^^^ parsing link title
		res = state.md.helpers.parseLinkTitle(state.src, pos, state.posMax);
		if (pos < max && start !== pos && res.ok) {
			title = res.str;
			pos = res.pos;

		// [link](  <href>  "title"  )
		//                         ^^ skipping these spaces
		for (; pos < max; pos++) {
			code = state.src.charCodeAt(pos);
			if (!md.utils.isSpace(code) && code !== 0x0A)
				break;
		}
		} else {
			title = '';
		}

		if (pos >= max || state.src.charCodeAt(pos) !== 0x29) { // Parenthesis: )
			state.pos = oldPos;
			return false;
		}
		pos++;
	} else {
		//
		// Link reference
		//
		if (typeof state.env.references === 'undefined')
			return false;

		if (pos < max && state.src.charCodeAt(pos) === 0x5B) { // Bracket: [
			start = pos + 1;
			pos = state.md.helpers.parseLinkLabel(state, pos);
			if (pos >= 0) {
				label = state.src.slice(start, pos++);
			} else {
				pos = labelEnd + 1;
			}
		} else {
			pos = labelEnd + 1;
		}

		// covers label === '' and label === undefined
		// (collapsed reference link and shortcut reference link respectively)
		if (!label)
			label = state.src.slice(labelStart, labelEnd);

		ref = state.env.references[md.utils.normalizeReference(label)];
		if (!ref) {
			state.pos = oldPos;
			return false;
		}
		href = ref.href;
		title = ref.title;
	}

	state.pos = pos;
	state.posMax = max;

	if (silent)
		return true;

	// We found the end of the link, and know for a fact it's a valid link;
	// so all that's left to do is to call tokenizer.
	const content = state.src.slice(labelStart, labelEnd);

	state.md.inline.parse(
		content,
		state.md,
		state.env,
		tokens = []
	);

	const mediaType = guessMediaType(href);
	const tag = mediaType == 'image' ? 'img' : mediaType;

	const token = state.push(mediaType, tag, 0);
	token.attrs = attrs = [['src', href]];
	if (mediaType == 'image')
		attrs.push(['alt', '']);

	token.children = tokens;
	token.content = content;

	if (title)
		attrs.push(['title', title]);

	state.pos = pos;
	state.posMax = max;
	return true;
}

const fullYoutubeRegex = /(?:(?:https?:)?(?:\/\/)?)(?:(?:www)?\.)?(?:youtube|youtu)\.(?:com|be)\/(?:(?:watch\?v=)|(?:embed\/)?)([a-zA-Z0-9_-]{11})(?:\?t=(\d+))?/i;
const shortYoutubeRegex = /(?:(?:https?:)?(?:\/\/)?)?youtu\.be\/([a-zA-Z0-9_-]{11})(?:\?t=(\d+))?/i;
const vimeoRegex = /(?:(?:https?:)?(?:\/\/)?)(?:(?:www)?\.)?vimeo.com\/(\d+)/i;
const streamableRegex = /https:\/\/streamable.com(?:\/\w+)?\/(\w+)/i;
const soundcloudRegex = /https?:\/\/(?:www.)?soundcloud.com\/([\w-]+\/[\w-]+)/i;

/**
 * Guess the media type represented by a URL based on the file extension,
 * if any
 *
 * @param url
 *  any valid URL
 * @returns
 *  a type identifier: 'image' (default for all unrecognized URLs), 'audio'
 *  or 'video'
 */
function guessMediaType(url: string): string {
	if (url.match(shortYoutubeRegex) || url.match(fullYoutubeRegex) || url.match(streamableRegex) || url.match(vimeoRegex) || url.match(soundcloudRegex)) {
		return 'iframe';
	}

	const extensionMatch = url.match(/\.([^/.]+)$/);
	if (extensionMatch === null)
		return 'image';
	const extension = extensionMatch[1];
	if (validAudioExtensions.indexOf(extension.toLowerCase()) != -1)
		return 'audio';
	else if (validVideoExtensions.indexOf(extension.toLowerCase()) != -1)
		return 'video';
	else
		return 'image';
}

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

function renderIframe(url, md: MarkdownIt) {
	const youtubeData = shortYoutubeRegex.exec(url) || fullYoutubeRegex.exec(url);
	if (youtubeData) {
		const data = youtubeData.map(data => md.utils.escapeHtml(data) as string);
		return makeIFrame(`https://www.youtube.com/embed/${data[1]}?rel=0${data[2] ? '&t='+data[2] : ''}`);
	}

	const streamable = streamableRegex.exec(url);
	if (streamable) {
		return makeIFrame(`https://streamable.com/s/${md.utils.escapeHtml(streamable[1])}`);
	}

	const vimeo = vimeoRegex.exec(url);
	if (vimeo) {
		return makeIFrame(`https://player.vimeo.com/video/${md.utils.escapeHtml(vimeo[1])}`, 640, 266);
	}

	const soundCloud = soundcloudRegex.exec(url);
	if (soundCloud) {
		return makeIFrame(`https://w.soundcloud.com/player/?url=https://soundcloud.com/${md.utils.escapeHtml(soundCloud[1])}`, 560, 166);
	}
}


/**
 * Render tokens of the video/audio type to HTML5 tags
 */
function renderMedia(tokens: Token[], idx: number, options: Options, env, md: MarkdownIt, videoAttrs?: string, audioAttrs?: string): string {
	const token = tokens[idx];
	const type = token.type;
	if (type !== 'video' && type !== 'audio' && type !== 'iframe' || !token.attrs)
		return '';

	let attrs;
	if (type == 'video' || type == 'audio') {
		attrs = type == 'video' ? videoAttrs : audioAttrs;
		if (attrs)
			attrs = ' ' + attrs.trim();
	} else {
		attrs = '';
	}

	// We'll always have a URL for non-image media: they are detected by URL
	const url = token.attrs[token.attrIndex('src')][1];

	if (type == 'iframe') {
		const html = renderIframe(url, md);
		if (html) {
			return html;
		}
	}

	// Title is set like this: ![descriptive text](video.mp4 "title")
	const title = token.attrIndex('title') != -1 ?
		` title="${md.utils.escapeHtml(token.attrs[token.attrIndex('title')][1])}"` :
		'';

	const fallbackText = translate(env.language, `html5 ${type} not supported`) + '\n' +
		translate(env.language, 'html5 media fallback link', [url]);

	const description = token.content ?
		'\n' + translate(env.language, 'html5 media description', [md.utils.escapeHtml(token.content)]) :
		'';

	return `<${type} src="${url}"${title}${attrs}>\n` +
		`${fallbackText}${description}\n` +
		`</${type}>`;
}


type MediaOptions = {
	videoAttrs?: string,
	audioAttrs?: string,
	translateFn?: (str: string) => string,
	messages?: any
}

/**
 * The main plugin function, exported as module.exports
 *
 * @param md
 *  instance, automatically passed by md.use
 * @param options
 *  configuration
 */
function html5Media(md: MarkdownIt, options: MediaOptions = {}) {
	if (options.messages)
		messages = options.messages;
	if (options.translateFn)
		translate = options.translateFn;

	const videoAttrs = options.videoAttrs !== undefined ? options.videoAttrs : 'controls class="html5-video-player"';
	const audioAttrs = options.audioAttrs !== undefined ? options.audioAttrs : 'controls class="html5-audio-player"';

	md.inline.ruler.at('image', (tokens, silent) => tokenizeImagesAndMedia(tokens, silent, md));

	md.renderer.rules.video = md.renderer.rules.audio = md.renderer.rules.iframe = (tokens, idx, opt, env) => {
		return renderMedia(tokens, idx, opt, env, md, videoAttrs, audioAttrs);
	};
}

export {
	html5Media,
	messages, // For partial customization of messages
	guessMediaType
};