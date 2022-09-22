import { remove } from '@antfu/utils';
interface YesNoModalOptions {
    title?: string,
    desc?: string,
    descType?: string,
    yes?: (close?: () => void, error?: (e) => void) => Promise<void>,
    no?: (close?: () => void, error?: (e) => void) => Promise<void>
}

export default function() {
    const yesNoModals = useState<YesNoModalOptions[]>('yesNoModals', () => []);

    return function(options: YesNoModalOptions) {
        const modal = {
            modelValue: ref(true),
            ...options,
            async yes(close, error) {
                try {
                    if (options.yes) {
                        await options.yes(close, error);
                    }
                    modal.modelValue.value = false;
                } catch (e) {
                    error(e);
                }
            },
            async no(close, error) {
                try {
                    if (options.no) {
                        await options.no(close, error);
                    }
                    modal.modelValue.value = false;
                } catch (e) {
                    error(e);
                }
            },
            closed() {
                remove(yesNoModals.value, modal);
            }
        };
        yesNoModals.value.push(modal);
    };
}