<template>
    <m-flex class="input-container" inline wrap :column="!isCheckbox" :gap="isCheckbox ? 1 : 2">
        <label v-if="!isCheckbox && label" :class="{ required }" :for="labelId">
            <slot name="label">
                {{label}}
            </slot>
        </label>
        <m-flex v-if="!$slots.default">
            <input 
                v-if="type == 'color'"
                ref="input"
                v-bind="$attrs"
                v-model="vm"
                :class="classes"
                style="flex-grow: 1;"
                @input="$emit('update:modelValue', vm);"
            >
            <textarea 
                v-if="type == 'textarea'"
                ref="elementRef"
                v-model="vm"
                class="mw-input"
                :rows="rows"
                v-bind="$attrs"
                @update="$emit('update:modelValue', vm)"
            />
            <input 
                v-else-if="isCheckbox" 
                :id="labelId"
                v-bind="$attrs"
                ref="elementRef"
                v-model="vm"
                :class="classes"
                type="checkbox"
                @change="$emit('update:modelValue', vm);"
            >
            <input 
                v-else
                :id="labelId"
                v-bind="$attrs"
                ref="elementRef"
                v-model="vm"
                :class="classes"
                :type="type"
            >
        </m-flex>
        <slot v-else/>
        <label v-if="isCheckbox && label" :for="labelId" class="flex-grow">
            <slot name="label">
                <span class="align-middle">{{label}}</span>
            </slot>
        </label>
        <span v-if="err" class="text-danger">{{err}}</span>
        <hr v-if="isCheckbox && desc">
        <small v-if="desc">{{desc}}</small>
    </m-flex>
</template>

<script setup lang="ts">
const props = defineProps<{
    id?: string,
    desc?: string,
    label?: string|boolean,
    validity?: string,
    rows?: number|string,
    type?: string,
    value?: string,
    required?: boolean,
}>();
const emit = defineEmits(['update:elementRef', 'update:modelValue']);
const vm = defineModel<any>('modelValue');
const elementRef = defineModel<HTMLInputElement>('elementRef', { local: true });
const uniqueId = useId();
const err = useWatchValidation(vm, elementRef);

const labelId = ref(props.id || uniqueId);
const isCheckbox = computed(() => props.type == 'checkbox');
const classes = computed(() => ({'mw-input': true, 'input-error': !!err.value}));

watch(elementRef, val => emit('update:elementRef', val));
watch(() => props.validity, val => {
    if (elementRef.value) {
        if (val) {
            elementRef.value.setCustomValidity(val);
        } else {
            elementRef.value.setCustomValidity('');
        }
    }
});
</script>

<style scoped>
.required:after {
    content: ' *';
    color: rgb(255, 88, 88);
}
</style>

<style>
.flex > .input-container:not(.flex-col > .input-container) {
    flex: 1;
}

.input-bg {
    flex: 1;
    color: var(--text-color);
    background-color: var(--input-bg-color);
    border: 1px solid var(--input-border-color);
    border-radius: var(--border-radius);
}

.mw-input {
    padding: 0.6rem;
    flex: 1;
    transition: border-color 0.25s;
    height: auto;
    color: var(--text-color);
    background-color: var(--input-bg-color);
    border: 1px solid var(--input-border-color);
    border-radius: var(--border-radius);
    resize: vertical;
}

.mw-input:focus-visible {
    outline: none;
    border-color: var(--primary-color)
}

.mw-input-invalid {
    border-color: var(--danger-color)
}

.mw-input[type='color'] {
    flex: 6;
}

.mw-input[type='checkbox'] {
    width: revert;
    margin-top: 3px;
}

.mw-input:disabled {
    opacity: 0.4;
}
</style>