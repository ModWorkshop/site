import en from "./en";

const owo = {
    ...en,
};

const kaomojis =[
    'ヾ(≧ ▽ ≦)ゝ',
    '( •̀ ω •́ )y',
    'UwU',
    'OwO',
    'UmU',
    '(^///^)',
    '`(*>﹏<*)′`',
    '(。・ω・。)',
    '┏ (゜ω゜)=☞',
    'o(≧口≦)o',
    '(⊙_⊙;)',
    '(ノω<。)ノ))☆.。',
    '(≧∇≦)ﾉ',
    '(≧﹏ ≦)',
    'ヾ(≧へ≦)〃',
    '(´･ω･`)?'
];

//For anyone confused, this is just a fun easter egg from 
function owoify(word: string) {
    return word.toLowerCase()
        .replace(/([rl])/, 'w')
        .replace(/n([aeiou])/, 'ny$1')
        .replace('th', 'd')
        .replace('.', kaomojis[Math.floor(Math.random() * kaomojis.length)])
        .replace('you', 'u');
}

for (const [key, word] of Object.entries(owo)) {
    owo[key] = owoify(word);
}

export default owo;