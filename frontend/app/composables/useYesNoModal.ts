import { remove } from '@antfu/utils';
interface YesNoModalOptions {
    title?: string,
    desc?: string,
    descType?: string,
    yes?: (error?: (e) => void) => Promise<void>,
    no?: (error?: (e) => void) => Promise<void>
}

export interface YesNoModal extends YesNoModalOptions {
    modelValue: Ref<boolean> & boolean,
    closed: () => void
}

export default function() {
    const yesNoModals = useState<YesNoModalOptions[]>('yesNoModals', () => []);

    return function(options: YesNoModalOptions) {
        const modal = {
            modelValue: ref(true),
            ...options,
            async yes(error) {
                try {
                    if (options.yes) {
                        await options.yes(error);
                    }
                    modal.modelValue.value = false;
                } catch (e) {
                    error(e);
                }
            },
            async no(error) {
                try {
                    if (options.no) {
                        await options.no(error);
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