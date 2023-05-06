function range(size, startAt = 1) {
    return Array.from(Array(size).keys()).map(i => i + startAt);
}

const sideStrings = ['', 'l', 'r', 'x', 'y', 'b', 't'];
const safelist = [];
for (const side of sideStrings) {
    safelist.push(...range(16).map(i => `p${side}-${i}`));
    safelist.push(...range(16).map(i => `m${side}-${i}`));
}

export default {
    corePlugins: {
        preflight: false,
    },
    safelist
};