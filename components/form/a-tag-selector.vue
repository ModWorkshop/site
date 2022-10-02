<template>
    <a-input>
        <flex wrap>
            <a-tag v-for="item of selectedOptions" :key="item.id" :color="item.color">
                <font-awesome-icon v-if="showItem(item)" class="cursor-pointer text-md" icon="circle-xmark" @click="deselectItem(item)"/> {{item.name}}
            </a-tag>
            <Popper>
                <a-button icon="plus" size="sm"/>
                <template #content>
                    <a-input v-model="search"/>
                    <a-dropdown-item v-for="item of filtered" :key="item.id" @click="selectItem(item)">{{item.name}}</a-dropdown-item>
                </template>
            </Popper>
        </flex>
    </a-input>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';

const props = withDefaults(defineProps<{
    options: any[];
    modelValue: string|number|any[];
    optionEnabled?: (option) => boolean,
    multiple?: boolean
}>(), { multiple: true });

const search = ref('');

const emit = defineEmits<{
    (e: 'update:modelValue', value: string|number|any[]): void
}>();

const selected = computed(() => {
    return typeof props.modelValue == 'object' ? props.modelValue : [props.modelValue];
});

function showItem(item) {
    return !props.optionEnabled || props.optionEnabled(item);
}

const filtered = computed(() => {
    const searchLower = search.value.toLowerCase();
    let options = props.options;
    if (searchLower) {
        options = options.filter(item => item.name.toLowerCase().match(searchLower));
    }

    if (props.multiple && typeof props.modelValue == 'object') {
        return options.filter(item => showItem(item) && !selected.value.includes(item.id));
    } else {
        return options.filter(item => showItem(item) && selected.value === item.id);
    }
});

const selectedOptions = computed(() => props.options.filter(item => selected.value.includes(item.id)));

function selectItem(item) {
    if (props.multiple && typeof props.modelValue == 'object') {
        if (!selected.value.includes(item.id)) {
            props.modelValue.push(item.id);
        }
        emit('update:modelValue', props.modelValue);
    } else {
        emit('update:modelValue', item.id);
    }
}

function deselectItem(item) {
    if (props.multiple && typeof props.modelValue == 'object') {
        remove(props.modelValue, item.id);
        emit('update:modelValue', props.modelValue);
    } else {
        emit('update:modelValue', undefined);
    } 
}
</script>

<style scoped>

</style>