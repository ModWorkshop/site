<template>
    <Teleport to="body">
        <Transition>
            <Suspense>
                <m-flex v-if="modelValue" class="modal" @click.self="$emit('update:modelValue', false)">
                    <m-flex column :class="classes" v-bind="$attrs">
                        <m-flex>
                            <slot name="title">
                                <h2 v-if="title">{{title}}</h2>
                                <i-mdi-close class="cursor-pointer ml-auto text-xl" @click="$emit('update:modelValue', false)"/>
                            </slot>
                        </m-flex>
                        <slot/>
                    </m-flex>
                </m-flex>
            </Suspense>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
    modelValue: boolean,
    size?: 'lg' | 'md' | 'sm',
    title?: string,
}>(), { size: 'md' });

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void,
    (e: 'opened'): void,
    (e: 'closed'): void
}>();

watch(() => props.modelValue, val => {
    if (val) {
        document.body.classList.add('modal-open');
    } else {
        document.body.classList.remove('modal-open');
    }

    if (val) {
        emit('opened');
    } else {
        emit('closed');
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
.modal {
    z-index: 400;
    display: flex;
    position: fixed;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: #00000080;
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

<style>
@media (max-width:1024px) {
    .modal-lg, .modal-md, .modal-sm {
        max-width: 90% !important;
    }
}

.modal-open {
    overflow-y: hidden;
}
</style>