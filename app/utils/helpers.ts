import type { Game, Notification } from '~/types/models';
import { partial } from "filesize";
import { serialize } from "object-to-formdata";
import type { LocationQueryValueRaw } from "vue-router";
import humanizeDuration from 'humanize-duration';
import { addDays, formatDuration, interval, intervalToDuration, parseISO } from 'date-fns';
import { ColorSpace, HSL } from 'colorjs.io/fn';

ColorSpace.register(HSL);

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

export function getDuration(fromDate?: string, toDate?: string) {
    if (!fromDate || !toDate) {
        return null;
    }

    return formatDuration(intervalToDuration({
        start: parseISO(fromDate),
        end: parseISO(toDate)
    }));
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

export async function reloadToken() {
    getRequest('/sanctum/csrf-cookie');
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
    const copy = {...data};
    for (const [key, value] of Object.entries(copy)) {
        if (value instanceof Array && value.length == 0) {
            copy[key] = null; // Thanks web standards for not supporting something as simple as a FUCKING EMPTY ARRAY
        }
    }

    return serialize(copy, { booleansAsIntegers: true, allowEmptyArrays: true, indices: true });
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
        return src && (src.startsWith("http://") || src.startsWith("https://") || src.startsWith("data:") || src.startsWith('blob:'));
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
    return addDays(Date.now(), 999999);
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
        return '#fff'; // fall back to white          
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

let count = 1;
const idMap = new WeakMap();
export function getObjectId(object) {
  const objectId = idMap.get(object);
  if (objectId === undefined) {
    count += 1;
    idMap.set(object, count);

    return count;
  }

  return objectId;
}

export const donationSites = {
    bmc: /(?:https:\/\/)?(?:www\.)?buymeacoffee\.com\/(\w+)/,
    kofi: /(?:https:\/\/)?(?:www\.)?ko-fi\.com\/(\w+)/,
    paypalme: /(?:https:\/\/)?(?:www\.)?paypal\.me\/(\w+)/,
    paypalBtn: /(?:https:\/\/)?(?:www\.)?paypal(?:\.me|\.com)\/donate\/\?hosted_button_id=(\w+)/,
    paypal: /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/,
    github: /(?:https:\/\/)?(?:www\.)?github\.com\/sponsors\/(\w+)/,
    boosty: /(?:https:\/\/)?(?:www\.)?boosty\.to\/(\w+)/
}

export function linkToDonationType(l?: string) {
    if (l) {
        for (const [k,v] of Object.entries(donationSites)) {
            if (v.test(l)) {
                return k;
            }
        }
    }
    return null;
}