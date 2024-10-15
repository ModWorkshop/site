import type { PluginWithOptions } from "markdown-it";
import type { RuleBlock } from "markdown-it/lib/parser_block.mjs";
import type { RenderRule } from "markdown-it/lib/renderer.mjs";

export type FencePluginOptions = {
    name: string;
    minMarkers?: number;
    marker: string;
    validate?: (params: string) => boolean,
    render?: RenderRule
}

export const fence: PluginWithOptions<FencePluginOptions> = function(md, opts) {
    function defaultValidate() {
        return true;
    }

    const defaultRender: RenderRule = function(tokens, idx) {
        return `<div>${md.render(tokens[idx].content)}</div>`;
    };

    const options = Object.assign({
        validate: defaultValidate,
        render: defaultRender
    }, opts);

    const name = opts?.name ?? 'ERROR';
    const minMarkers = opts?.minMarkers ?? 3;

    const fence: RuleBlock = function(state, startLine, endLine) {
        const optionMarker = options.marker || '`';
        let pos = state.bMarks[startLine] + state.tShift[startLine];
        const max = state.eMarks[startLine];

        if (state.sCount[startLine] - state.blkIndent > minMarkers) return false;
        if (pos + minMarkers > max) return false;

        const marker = state.src.charCodeAt(pos);

        if (marker !== optionMarker.charCodeAt(0)) return false;

        const mem = pos;
        pos = state.skipChars(pos, marker);
        let len = pos - mem;

        if (len < minMarkers) return false;

        const markup = state.src.slice(mem, pos);
        const params = state.src.slice(pos, max);

        if (params.indexOf(String.fromCharCode(marker)) >= 0) return false;

        let nextLine = startLine + 1;

        let haveEndMarker = false;
        let endMem = mem;
        let endPos = pos;
        let endMax = max;

        for (; nextLine < endLine; nextLine++) {
            endPos = endMem = state.bMarks[nextLine] + state.tShift[nextLine];
            endMax = state.eMarks[nextLine];

            if (endPos < endMax && state.sCount[nextLine] < state.blkIndent) break;

            if (state.src.charCodeAt(endPos) !== marker) continue;
            if (state.sCount[nextLine] - state.blkIndent > minMarkers) continue;

            endPos = state.skipChars(endPos, marker);

            if (endPos - endMem < len) continue;

            endPos = state.skipSpaces(endPos);

            if (endPos < endMax) continue;

            haveEndMarker = true;

            break;
        }

        if (!haveEndMarker) {
            return false;
        }

        len = state.sCount[startLine];
        state.line = nextLine + (haveEndMarker ? 1 : 0);

        if (!options.validate(params)) return false;
        const token = state.push(name, 'div', 0);
        token.info = params;

        token.content = state.getLines(startLine + 1, nextLine, len, true);
        token.markup = markup;
        token.map = [startLine, state.line];

        return true;
    };

    md.block.ruler.before('fence', name, fence, {
        alt: ['paragraph', 'reference', 'blockquote', 'list']
    });
    md.renderer.rules[name] = options.render;
};