import { Game } from './../types/models';
import { partial } from "filesize";
import { DateTime } from 'luxon';
import { serialize } from "object-to-formdata";
import { LocationQueryValueRaw } from "vue-router";

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
 * @param {String} t 
 * @returns String
 */
export function getTimeAgo(t: string) {
    return DateTime.fromISO(t).toRelative();
}

/**
 * Converts ISO8601 date to full date format.
 */
export function fullDate(t: string): string {
    return DateTime.fromISO(t).toLocaleString(DateTime.DATETIME_SHORT);
}


const million = Math.pow(10, 6);

/**
 * Converts a big number like 159,125 to 159k
 */
export function shortStat(n: number): string {
    if (n < 1000) {
        return n.toString();
    } else if (n < million) {
        return Math.round(n / 1000) + 'K';
    } else {
        return Math.round(n / million) + 'M';
    }
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
    await useGet('/sanctum/csrf-cookie');
}

export function setQuery(key: string, value: LocationQueryValueRaw | LocationQueryValueRaw[]) {
    const router = useRouter();
    const route = useRoute();

    console.log({ ...route.query, [key]: value });
    

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
export function isSrcExternal(src: string) {
    return src && (src.startsWith("http://") || src.startsWith("https://") || src.startsWith("data:"));
}

export function getObjectLink(type: string, object: Record<string, unknown>) {
    if (!object) {
        return null;
    }
    switch(type) {
        case 'mod':
            return `/mod/${object.id}`;
        case 'thread':
            return `/thread/${object.id}`;
        case 'game':
            return `g/${object.short_name}`;
    }

    return null;
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