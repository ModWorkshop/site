import en from '../en/notifications';

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
        .replace(/[RL]/, 'W')
        .replace(/N[aeiou]/, 'Ny$1')
        .replace('th', 'd')
        .replace('you', 'u');
}

for (const [key, word] of Object.entries(en)) {
    en[key] = owoify(word);
}

export default en;