<template>
    <dialog :open="delayedVm" :class="{'modal-dialog': true, 'modal-closed': !vm}">
        <m-flex v-if="delayedVm" class="modal" @click.self="vm = false">
            <m-flex column :class="classes" v-bind="$attrs">
                <m-flex>
                    <slot name="title">
                        <h2 v-if="title">{{title}}</h2>
                        <i-mdi-close class="cursor-pointer ml-auto text-xl" @click="vm = false"/>
                    </slot>
                </m-flex>
                <slot/>
            </m-flex>
        </m-flex>
    </dialog>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    size?: 'lg' | 'md' | 'sm',
    title?: string,
}>(), { size: 'md' });

const emit = defineEmits<{
    (e: 'opened'): void,
    (e: 'closed'): void
}>();

const vm = defineModel({ default: false, local: true });
const delayedVm = ref(vm.value);

watch(vm, val => {
    if (val) {
        delayedVm.value = true;
        emit('opened');
    } else {
        emit('closed');
        setTimeout(() => {
            delayedVm.value = false;
        }, 200);
    }
});

const classes = computed(() => ({
    'modal-body': true,
    'modal-lg': props.size == 'lg',
    'modal-md': props.size == 'md',
    'modal-sm': props.size == 'sm',
}));
</script>

<style scoped>
@keyframes modalBackdropOpen {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes modalBackdropClose {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

@keyframes modalSlideOpen {
    from {
        transform: translateY(-16px);
    }
    to {
        transform: translateY(0);
    }
}

@keyframes modalSlideClose {
    from {
        transform: translateY(0);
    }
    to {
        transform: translateY(-16px);
    }
}
.modal-dialog[open] > .modal {
    animation-name: modalSlideOpen;
}

.modal-closed > .modal {
    animation-name: modalSlideClose !important;
}

.modal-dialog {
    background-color: #00000080;
    z-index: 400;
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    animation-duration: 0.25s;
    will-change: transform, opacity;
}

.modal-dialog[open] {
    animation-name: modalBackdropOpen;
}

.modal-closed {
    animation-name: modalBackdropClose !important;
}
.modal {
    display: flex;
    position: fixed;
    width: 100%;
    height: 100%;
    animation-duration: 0.25s;
    will-change: transform, opacity;
}

.modal-lg {
    max-width: 90%;
    width: 1000px;
}

.modal-md {
    max-width: 75%;
    width: 800px;
}

.modal-sm {
    max-width: 60%;
    width: 600px;
}

.modal-body {
    max-height: 90%;
    margin: auto;
    overflow-y: auto;
    color: var(--text-color);
    background-color: var(--content-bg-color);
    border-radius: var(--border-radius);
    padding: 1rem;
}
</style>