import { remove } from "@antfu/utils";

interface ToastOptions {
    title?: string;
    desc?: string;
    color?: string;
    duration?: number|false;
}

interface Toast extends ToastOptions {
    key?: string;
}

/** Vuestic unfortunately auto imports "useToast" */

export default function() {
    const toasts = useState('toasts', () => []);

    return {
        showToast(options: ToastOptions) {
            const toast: Toast = options;
            toast.key = `toast_${ Math.random().toString(16)}`;
            toasts.value.push(toast);

            if (options.duration !== false) {
                setTimeout(() => {
                    remove(toasts.value, toast);
                }, options.duration ?? 5000);
            }

            return toast;
        },
        closeAllToasts() {
            toasts.value = [];
        },
        closeToast(toast: Toast) {
            remove(toasts.value, toast);
        }
    };
}