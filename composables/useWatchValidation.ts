import { Ref } from "nuxt/dist/app/compat/capi";

/**
 * Watches watchVal and triggers native validation errors, setting them to a ref.
 */
export default function(watchVal: Ref, element: Ref<HTMLTextAreaElement|HTMLInputElement>) {
    const error = ref();

    let timeoutId = null;

    watch(watchVal, () => {
        if (element.value) {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            timeoutId = setTimeout(() => {
                error.value = element.value.validationMessage;
            }, 500);
        }
    });

    return error;
}