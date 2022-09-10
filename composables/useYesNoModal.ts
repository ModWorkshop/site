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
        const length = yesNoModals.value.push({
            ...options,
            async yes(close, error) {
                try {
                    if (options.yes) {
                        await options.yes(close, error);
                    }
                    yesNoModals.value.splice(length-1, 1);
                } catch (e) {
                    error(e);
                }
            },
            async no(close, error) {
                try {
                    if (options.no) {
                        await options.yes(close, error);
                    }
                    yesNoModals.value.splice(length-1, 1);
                } catch (e) {
                    error(e);
                }
                yesNoModals.value.splice(length-1, 1);
            },
        });
    };
}