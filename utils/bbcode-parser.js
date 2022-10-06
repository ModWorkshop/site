import { getContrast } from "polished";

const colorNamePattern = /^(?:aliceblue|antiquewhite|aqua|aquamarine|azure|beige|bisque|black|blanchedalmond|blue|blueviolet|brown|burlywood|cadetblue|chartreuse|chocolate|coral|cornflowerblue|cornsilk|crimson|cyan|darkblue|darkcyan|darkgoldenrod|darkgray|darkgreen|darkkhaki|darkmagenta|darkolivegreen|darkorange|darkorchid|darkred|darksalmon|darkseagreen|darkslateblue|darkslategray|darkturquoise|darkviolet|deeppink|deepskyblue|dimgray|dodgerblue|firebrick|floralwhite|forestgreen|fuchsia|gainsboro|ghostwhite|gold|goldenrod|gray|green|greenyellow|honeydew|hotpink|indianred|indigo|ivory|khaki|lavender|lavenderblush|lawngreen|lemonchiffon|lightblue|lightcoral|lightcyan|lightgoldenrodyellow|lightgray|lightgreen|lightpink|lightsalmon|lightseagreen|lightskyblue|lightslategray|lightsteelblue|lightyellow|lime|limegreen|linen|magenta|maroon|mediumaquamarine|mediumblue|mediumorchid|mediumpurple|mediumseagreen|mediumslateblue|mediumspringgreen|mediumturquoise|mediumvioletred|midnightblue|mintcream|mistyrose|moccasin|navajowhite|navy|oldlace|olive|olivedrab|orange|orangered|orchid|palegoldenrod|palegreen|paleturquoise|palevioletred|papayawhip|peachpuff|peru|pink|plum|powderblue|purple|red|rosybrown|royalblue|saddlebrown|salmon|sandybrown|seagreen|seashell|sienna|silver|skyblue|slateblue|slategray|snow|springgreen|steelblue|tan|teal|thistle|tomato|turquoise|violet|wheat|white|whitesmoke|yellow|yellowgreen)$/;
const colorCodePattern = /^#?[a-fA-F0-9]{6}$/;
const regex = {
    tags: RegExp(/(\[(\/)?(\w+|\*+)(?:[ =](.*?))?\])(\r\n|\r|\n)?(?:(?!\(https\)))/g), //Although I changed this to not parse md links we still try to avoid matching them in the first place.
    newline: RegExp(/(?:\r\n|\r|\n)/g),
    placeholders: RegExp(/\[TAG-[1-9]{1,}\]/g)
};
const checkColorBg = '#1a1c1e';
const tags = {
    b: 'strong',
    u: 'u',
    i: 'em',
    s: 'del',
    list: 'ul',
    '*': {
        open: '<li>',
        special: true,
        close: null
    },
    hr: {
        open: '<hr>',
        special: true,
        close: null
    },
    url: {
        open: (attr) => `<a href="${attr || '#'}">`,
        close: '</a>'
    },
    quote: {
        open: (attr) => `<blockquote author="${attr || ''}">`,
        close: '</blockquote>'
    },
    spoiler: {
        open: '<div><details><summary>Spoiler!</summary>',
        close: '</details></div>'
    },
    center: {
        open: '<div class="center">',
        close: '</div>',
        block: true
    },
    left: {
        open: '<div class="left">',
        close: '</div>',
        block: true
    },
    right: {
        open: '<div class="right">',
        close: '</div>',
        block: true
    },
    code: {
        type: 'content',
        replace: (_, content) => `<pre><code class="language">${content.replace(/&nbsp;/g, ' ')}</code></pre>`,
        noParse: true,
        block: true
    },
    php: {
        type: 'content',
        replace: (_, content) => `<pre><code class="language-php">${content.replace(/&nbsp;/g, ' ')}</code></pre>`,
        noParse: true,
        block: true
    },
    color: {
        type: 'content',
        replace: (attr, content) => {
            if (!content) {
                return '';
            }

            let colorCode = attr || "black";
            colorNamePattern.lastIndex = 0;
            colorCodePattern.lastIndex = 0;
            if (!colorNamePattern.test(colorCode)) {
                if (!colorCodePattern.test(colorCode))
                    colorCode = "black";
                else if (colorCode.substr(0, 1) !== "#")
                    colorCode = "#" + colorCode;
            }

            if (getContrast(colorCode, checkColorBg) > 3.5)
                return `<span style="color:${colorCode}">${content}</span>`;
            else
                return content;
        }
    },
    size: {
        open: params => {
            params = params || '';

            let size;

            switch (params) {
                case 'xx-small':
                    size = 'small';
                    break;
                case 'x-small':
                    size = 'small';
                    break;
                case 'small':
                    size = 'small';
                    break;
                case 'large':
                    size = 'large';
                    break;
                case 'x-large':
                    size = 'x-large';
                    break;
                case 'xx-large':
                    size = 'x-large';
                    break;
                default:
                    return '<span>';
            }

            return `<span style="font-size:${size}">`;
        },
        close: '</span>'
    },
    img: {
        type: 'content',
        noParse: true,
        replace: (attr, content) => {
            if (!content)
                return '';

            return `<img src="${content}" alt="${attr || ''}"/>`;
        }
    },
    video: {
        type: 'content',
        noParse: true,
        replace: (params, content) => {
            let w = 560, h = 315, src = '';
            switch (params) {
                case 'youtube': {
                    const id = content.replace(/(?:(?:https?:)?(?:\/\/)?)(?:(?:www)?\.)?(?:youtube|youtu)\.(?:com|be)\/(?:(?:watch\?v=)|(?:embed\/)?)([a-zA-Z0-9_-]{11})/g, '$1');
                    src = 'https://www.youtube.com/embed/' + id;
                    break;
                }
                case 'streamable': {
                    const id = content.replace(/https:\/\/streamable.com(?:\/\w+)?\/(\w+)/g, '$1');
                    src = 'https://streamable.com/s/' + id;
                    break;
                }
                case 'vimeo': {
                    const id = content.replace(/(?:(?:https?:)?(?:\/\/)?)(?:(?:www)?\.)?vimeo.com\/(\d+)/g, '$1');
                    src = 'https://player.vimeo.com/video/' + id;
                    w = 640;
                    h = 266;
                    break;
                }
            }

            return `<iframe width="${w}" height="${h}" src="${src}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen></iframe>`;
        }
    },
    align: {
        open: params => {
            switch (params) {
                case 'center':
                    return '<div class="center">';
                case 'right':
                    return '<div class="right">';
                default:
                    return '<div class="left">';
            }
        },
        block: true,
        close: '</div>'
    },
    font: {},
    noparse: {},
    email: {},
};

const config = {
    newline: false,
    paragraph: false,
    cleanUnmatchable: false
};

function ignoreLoop(tagsMap, content) {
    tagsMap.forEach(tag => {
        content = content.replace('[TAG-' + tag.index + ']', tag.raw);
        if (tag.closing) {
            content = content.replace('[TAG-' + tag.closing.index + ']', tag.closing.raw);
        }
        if (tag.children.length) {
            content = ignoreLoop(tag.children, content);
        }

        tag.children = [];
    });
    return content;
}

function contentLoop(tagsMap, content) {
    tagsMap.forEach(tag => {
        let module = tags[tag.module];
        const isIgnore = tag.module === 'noparse';

        if (module.special || tag.closing) {
            if (isIgnore) {
                content = ignoreTag(tag, module, content);
            }
            else {
                content = processTag(tag, module, content);
            }
        }

        if (tag.children.length && !isIgnore) {
            content = contentLoop(tag.children, content);
        }
    });

    return content;
}

function tagLoop(tagsMap, cleanUp, parent) {
    for (let i = 0; i < tagsMap.length; i++) {
        const current = tagsMap[i];
        let found = false;
        if (current.isClosing || current.matchTag !== null) {
            continue; // already handled this tag / not closing
        }

        tagsMap.forEach((item, ii) => {
            if (found || current.matchTag !== null || ii <= i || item.matchTag !== null || item.foundOpening || !item.isClosing || current.module !== item.module)
                return;

            tagsMap[ii].matchTag = current.index;
            current.matchTag = item.index;
            found = ii; // next index
        });

        let childStart = i + 1;

        if (found !== false) {
            const closing = tagsMap[current.matchTag];
            closing.foundOpening = true;
            current.closing = closing;
        }
        else {
            current.matchTag = false;
            continue;
        }

        // sweep children
        if (childStart < found) {
            current.children = tagsMap.slice(childStart, found).map((child) => {
                child.parent = current.index;
                return child;
            });
        }

        let ii = childStart;
        while (ii < found) {
            tagsMap[ii].parent = current.index;
            ii++;
        }
    }

    // sweep children & matched closing tags
    tagsMap = tagsMap.filter(item => {
        if ((item.isClosing && !item.foundOpening) || (!item.special && !item.isClosing && !item.closing)) {
            cleanUp.push(item);
            return false;
        }

        return !(
            (item.limitParent && (item.parent === null || tagsMap[item.parent].module === item.limitParent))
            || (parent === undefined && item.parent !== null)
            || (item.parent !== null && item.parent !== parent)
            || item.isClosing
        );
    });

    return tagsMap.map((tag) => {
        if (tag.children.length) {
            tag.children = tagLoop(tag.children, cleanUp, tag.index);
        }

        return tag;
    });
}


function processTag(tag, module, content){            
    const isString = typeof module == 'string';

    if(!isString && module.special)
        return content.replace('[TAG-' + tag.index + ']', module.open);

    let openTag = "[TAG-" + tag.index + "]",
        closeTag = "[TAG-" + tag.closing.index + "]";
    let start = content.indexOf(openTag),
        end = content.indexOf(closeTag);

    let replace = '';
    let innerContent = content.substr(start + openTag.length, end - (start + openTag.length));
    
    if(!isString && module.noParse){
        innerContent = this._ignoreLoop(tag.children, innerContent);
        tag.children = [];
    }
    else if(isString || !module.retainNewLines)
        innerContent = innerContent.replace(/(?:\r\n+|\r|\n+)/g, '<br>');

    if(isString)
        replace = `<${module}>${innerContent}</${module}>`;
    else if(module.replace){
        replace = module.replace;
        if(typeof(replace) === 'function')
            replace = replace(tag.attr, innerContent);
    }
    else{
        let open = module.open || '', close = module.close || '';
        if(typeof(open) === 'function')
            open = open(tag.attr);

        if(typeof(close) === 'function')
            close = close(tag.attr);

        replace = open + innerContent + close;
    }

    let contentStart = content.substr(0, start),
        contentEnd = content.substr(end + closeTag.length);

    return contentStart + replace + contentEnd;
}

function ignoreTag(tag, module, content){
    let openTag = "[TAG-" + tag.index + "]";
    let start = content.indexOf(openTag);
    let closeTag = "";
    let end = content.length;
    if(tag.closing){
        closeTag = "[TAG-" + tag.closing.index + "]";
        end = content.indexOf(closeTag);
    }
    let innerContent = content.substr(start + openTag.length, end - (start + openTag.length));
    innerContent = this._ignoreLoop(tag.children, innerContent);
    let contentStart = content.substr(0, start),
        contentEnd = content.substr(end + closeTag.length);

    return contentStart + innerContent + contentEnd;
}

export default function parseBBCode(input) {
    let tagsMap = [];
    // split input into tags by index
    if (config.newline) {
        if (config.paragraph) {
            input = input.replace(regex.newline, "</p><p>");
        }
        else {
            input = input.replace(regex.newline, "<br/>");
        }
    }

    if (config.paragraph) {
        input = '<p>' + input + '</p>';
    }

    // handle when no tags are present
    if (!regex.tags.test(input)) {
        return input;
    }

    let i = -1;
    input = input.replace(regex.tags, (wholeMatch, rawTag, isClosing, tagName, tagProps, nl) => {
        tagName = tagName.toLowerCase();
        const module = tags[tagName];

        if (module) {
            isClosing = isClosing === '/';

            i++;
            tagsMap.push({
                index: i,
                module: tagName,
                isClosing: isClosing,
                raw: rawTag,
                attr: tagProps,
                special: module.special,
                limitParents: module.limitParents,
                closing: null,
                children: [],
                parent: null,
                matchTag: null
            });

            return '[TAG-' + i + ']' + ((nl !== undefined && isClosing) ? nl : ''); // placeholder for tag
        } else {
            return wholeMatch;
        }
    });

    // loop through each tag to create nested elements
    const cleanUp = [];
    tagsMap = tagLoop(tagsMap, cleanUp);

    // put back all non-found matches?
    input = contentLoop(tagsMap, input);
    input = ignoreLoop(cleanUp, input); //Revert tags that failed to parse.

    if (config.cleanUnmatchable) {
        input = input.replace(regex.placeholders, '');
    }

    return input;
}