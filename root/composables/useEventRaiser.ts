/**
 * An event raiser. Generally created due to me not liking the idea of calling events by incrementing values.
 * This is a more "clean" approach as it hides the mechanism.
 */

export interface EventRaiser {
    listen: Ref;
    execute: () => void
}

export default function(): EventRaiser {
    const listen = ref(1);

    return { 
        listen,
        execute() {
            listen.value++;
        }
    };
}