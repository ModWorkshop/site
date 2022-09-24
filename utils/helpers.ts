import fileSize from "filesize";
import { DateTime } from 'luxon';
import { serialize } from "object-to-formdata";
import { LocationQueryValueRaw } from "vue-router";

/**
 * Converts bytes to human readable KiB/MiB(Kibiytes/Mebibytes)/etc.
 */
export const friendlySize = fileSize.partial({base: 2});

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
export function timeAgo(t: string) {
    return DateTime.fromISO(t).toRelative();
}

/**
 * Converts ISO8601 date to full date format.
 * @param {String} t 
 * @returns String
 */
export function fullDate(t: string) {
    return DateTime.fromISO(t).toLocaleString(DateTime.DATETIME_SHORT);
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
    return serialize(data, { booleansAsIntegers: true });
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