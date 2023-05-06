import { remove } from "@antfu/utils";
import { Toast } from "~~/types/toast";

export default function() {
    const toasts = useState<Toast[]>('toasts', () => []);

    return {
        showToast(options: Toast) {
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