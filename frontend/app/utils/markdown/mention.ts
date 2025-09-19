import type { RuleInline } from 'markdown-it/lib/parser_inline.mjs';

export default function (md) {
	const mentionRule: RuleInline = function (state, silent) {
		let end = state.pos + 1;
		const max = state.posMax, start = state.pos;
		const prev = state.src.charCodeAt(start - 1);

		// A mention must begin with either nothing, line feed, tab or space. Otherwise ignore. Prevents highlighting things such as emails.
		if (silent || state.src.charCodeAt(start) !== 64/* @ */ || !(prev === 10 || prev === 9 || prev === 32 || Number.isNaN(prev))) {
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
		if (end - start === 1) {
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
		token.markup = '@';
		token.attrs = [['href', `/user/${content}`]];

		token = state.push('text', '', 0);
		token.content = '@' + content;

		token = state.push('link_close', 'a', -1);
		token.markup = '@';

		state.pos = state.posMax + 1;
		state.posMax = max;
		return true;
	};

	md.inline.ruler.after('emphasis', 'mention', mentionRule);
}
