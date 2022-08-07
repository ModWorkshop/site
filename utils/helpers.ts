import fileSize from "filesize";
import { DateTime } from 'luxon';
import { serialize } from "object-to-formdata";

/**
 * Converts bytes to human readable KiB/MiB(Kibiytes/Mebibytes)/etc.
 */
export const friendlySize = fileSize.partial({base: 2});

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

export function setQuery(key: string, value: string) {
    const router = useRouter();
    const route = useRoute();

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