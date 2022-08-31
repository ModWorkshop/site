interface YesNoModalOptions {
    title?: string,
    desc?: string,
    descType?: string,
    yes?: (ok?: () => void, error?: (e) => void) => Promise<void>,
    no?: () => void
}

export default function() {
    const yesNoModals = useState<YesNoModalOptions[]>('yesNoModals', () => []);

    return function(options: YesNoModalOptions) {
        const length = yesNoModals.value.push({
            ...options,
            async yes(ok, error) {
                try {
                    if (options.yes) {
                        await options.yes(ok, error);
                    }
                    yesNoModals.value.splice(length-1, 1);
                } catch (e) {
                    error(e);
                }
            },
            no() {
                if (options.no) {
                    options.no();
                }
                yesNoModals.value.splice(length-1, 1);
            },
        });
    };
}