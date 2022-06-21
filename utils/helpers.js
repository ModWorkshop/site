import fileSize from "filesize";
import { DateTime } from 'luxon';

/**
 * Converts bytes to human readable KiB/MiB(Kibiytes/Mebibytes)/etc.
 */
export const friendlySize = fileSize.partial({base: 2});

/**
 * Converts ISO8601 date to relative 'time ago' format.
 * @param {String} t 
 * @returns String
 */
export function timeAgo(t) {
    return DateTime.fromISO(t).toRelative();
}

/**
 * Converts ISO8601 date to full date format.
 * @param {String} t 
 * @returns String
 */
export function fullDate(t) {
    return DateTime.fromISO(t).toLocaleString(DateTime.DATE_SHORT);
}

export async function reloadCSRF() {
    const { $ftch } = useNuxtApp();
    await $ftch('/sanctum/csrf-cookie');
}