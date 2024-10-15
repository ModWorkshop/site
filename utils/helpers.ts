import type { Game, Notification } from '~~/types/models';
import { partial } from "filesize";
import { DateTime, Interval } from 'luxon';
import { serialize } from "object-to-formdata";
import type { LocationQueryValueRaw } from "vue-router";
import humanizeDuration from 'humanize-duration';

/**
 * Converts bytes to human readable KiB/MiB(Kibiytes/Mebibytes)/etc.
 */
export const friendlySize = partial({base: 2});

/**
 * Permissions that make the admin page available to the user
 */
export const adminPagePerms = [
    'admin',
    'manage-roles',
    'moderate-users',
    'manage-mods',
    'manage-tags',
    'manage-docs',
    'manage-users',
    'manage-forum-categories',
];

/**
 * Permissions that make the game admin page available to the user
 */
export const adminGamePagePerms = [
    'manage-game',
    'manage-roles',
    'moderate-users',
    'manage-mods',
    'manage-tags',
    'manage-docs',
    'manage-forum-categories',
];

export const colorSchemes = [
    'blue',
    'green',
    'pink',
    'red',
    'teal',
    'purple',
    'gray',
    'orange',
    'cyan',
];

/**
 * Converts ISO8601 date to relative 'time ago' format.
 */
export function getTimeAgo($t: (str: string) => string, t?: string): string {
    let timeAgo: string|null = '';
    if (t) {
        const dt = DateTime.fromISO(t);
        const diff = DateTime.now().diff(dt, ['seconds']).seconds;
        timeAgo = (diff > 0 && diff < 2) ? $t('moments_ago') : dt.toRelative();
    }
    
    return timeAgo || 'undefined time ago';
}

/**
 * Converts ISO8601 date to full date format.
 */
export function fullDate(t?: string): string {
    let timeAgo = '';
    if (t) {
        timeAgo = DateTime.fromISO(t).toLocaleString(DateTime.DATETIME_SHORT) || '';
    }
    
    return timeAgo || 'Undefined date';
}

export function getDuration($t: (str: string) => string, fromDate, toDate) {
    return toDate ? humanizeDuration(Interval.fromDateTimes(DateTime.fromISO(fromDate), DateTime.fromISO(toDate))
        .toDuration(), { units: ['mo', 'd', 'h'], round: true }) : $t('forever');
}

const million = Math.pow(10, 6);

/**
 * Converts a big number like 159,125 to 159k
 */
export function shortStat(n: number): string {
    if (n < 1000) {
        return n.toString();
    } else if (n < million) {
        return (n / 1000).toFixed(1) + 'K';
    } else {
        return (n / million).toFixed(1) + 'M';
    }
}

/**
 * Returns a friendly version of a number. 12345 -> 12,345 (based on locale)
 */
export function friendlyNumber(locale: string, n: number): string {
    return new Intl.NumberFormat(locale).format(n);
}

/**
 * Converts ISO8601 date to date format.
 * @param {String} t 
 * @returns String
 */
export function date(t: string) {
    return DateTime.fromISO(t).toLocaleString(DateTime.DATE_FULL);
}

export async function reloadToken() {
    useGet('/sanctum/csrf-cookie');
}

export function setQuery(key: string, value: LocationQueryValueRaw | LocationQueryValueRaw[]) {
    const router = useRouter();
    const route = useRoute();    

    router.replace({ query: { ...route.query, [key]: value } });
}

/**
 * Converts JS objects to FormData. Necessary if you want to uplaod files basically
 */
export function serializeObject(data: Record<string, unknown>) {
    return serialize(data, { booleansAsIntegers: true, allowEmptyArrays: true, indices: true });
}

/**
 * Replaces a specific range of a string with another string
 */
export function strReplacRange(str: string, start: number, end: number, replacement: string) {
    return str.substring(0, start)+replacement+str.substring(end);

}

/**
 * Returns whether or not the src URL is external. If it begins with a URL http/https or data: it's "external"
 */
export function isSrcExternal(src?: string|object|Blob) {
    if (typeof src == 'string') {
        return src && (src.startsWith("http://") || src.startsWith("https://") || src.startsWith("data:"));
    } else {
        return true;
    }
}

export function getObjectLink(type: string, object: Record<string, unknown|undefined|null>) {
    if (!object) {
        return undefined;
    }
    switch(type) {
        case 'mod':
            return `/mod/${object.id}`;
        case 'thread':
            return `/thread/${object.id}`;
        case 'game':
            return `/g/${object.short_name}`;
        case 'user':
            return `/user/${object.id}`;
        case 'comment':
            return `/${object.commentable_type}/${object.commentable_id}/${object.reply_to ? `post/${object.reply_to}` : ''}?comment=${object.id}`;
    }

    return undefined;
}

export function getGameResourceUrl(resource: string, game?: Game) {
    return game ? `games/${game.id}/${resource}` : resource;
}

//Length is checked by input
export function passwordValidity(password: string) {
    if (password) {
        if (!password.match(/[a-z]/)) {
            return 'password_error_lowercase';
        } else if (!password.match(/[A-Z]/)) {
            return 'password_error_uppercase';
        } else if (!password.match(/[0-9]/)) {
            return 'password_error_digit';
        }
    }
}

export function longExpiration() {
    return DateTime.now().plus({ years: 99 }).toJSDate();
}

//https://stackoverflow.com/a/18211298
export function clearAllCookies() {
    const cookies = document.cookie.split(";");
    for (let i = 0; i < cookies.length; i++){   
        document.cookie = cookies[i].split("=")[0] + "=;expires=Thu, 21 Sep 1979 00:00:01 UTC;";                                
    }
}

export function getAdminUrl(page: string, game?: Game) {
    return `${game ? `/g/${game.short_name}` : ''}/admin/${page}`;
}

export function truncate(str: string, length: number) {
    if (str.length < length) {
        return str;
    }

    return str.substring(0, length) + '...';
}

// https://stackoverflow.com/questions/9733288/how-to-programmatically-calculate-the-contrast-ratio-between-two-colors
function hexToInt(hex: string) {
    return parseInt(`0x${hex}`);
}

function hexToColor(color: string) {
    if (color[0] == '#') {
        color = color.substring(1);
    }

    if (color.length == 3) {
        return [hexToInt(color[0]+color[0]), hexToInt(color[1]+color[1]), hexToInt(color[2]+color[2])];
    } else if (color.length == 6) {
        return [hexToInt(color.substring(0, 2)), hexToInt(color.substring(2, 4)), hexToInt(color.substring(4, 6))];
    } else {
        return [255, 255, 255];
    }
}

const RED = 0.2126, GREEN = 0.7152, BLUE = 0.0722, GAMMA = 2.4;
function luminance(color: number[]) {
    const a = color.map((v) => {
        v /= 255;
        return (v <= 0.03928) ? (v / 12.92) : Math.pow((v + 0.055) / 1.055, GAMMA);
    });

    return a[0] * RED + a[1] * GREEN + a[2] * BLUE;
}

export function getContrast(color1: string|number[], color2: string|number[]) {
    const lum1 = luminance(typeof color1 == 'string' ? hexToColor(color1) : color1);
    const lum2 = luminance(typeof color2 == 'string' ? hexToColor(color2) : color2);

    return (Math.max(lum1, lum2) + 0.05) / (Math.min(lum1, lum2) + 0.05);
}

// https://stackoverflow.com/a/8046813
export function convertRGBDecimalToHex(rgb) {
    const regex = /rgb *\( *([0-9]{1,3}) *, *([0-9]{1,3}) *, *([0-9]{1,3}) *\)/;
    const values = regex.exec(rgb);
    if(!values || values.length != 4) {
        return rgb; // fall back to what was given.              
    }
    return "#" 
        + (Math.round(parseFloat(values[1])) + 0x10000).toString(16).substring(3).toUpperCase() 
        + (Math.round(parseFloat(values[2])) + 0x10000).toString(16).substring(3).toUpperCase()
        + (Math.round(parseFloat(values[3])) + 0x10000).toString(16).substring(3).toUpperCase();
}

// Marks all notifications as read, if needed updates a given array of notifications
export async function markAllNotificationsAsRead(notifications?: Notification[], count?: Ref<number|null>) {
    await postRequest('notifications/read-all');
    if (notifications) {
        notifications.forEach(item => item.seen = true);
    }

    if (count) {
        count.value = 0;
    }
}

/**
 * Returns the first non empty string from given arguments
 */
export function firstNonEmpty(...strs) {
    for (const s of strs) {
        if (typeof s == 'string' && s.length > 0) {
            return s;
        }
    }
    
    return null;
}