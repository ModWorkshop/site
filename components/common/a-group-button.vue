<template>
    <a-nav-link v-if="!buttonStyle || buttonStyle == 'nav'" :selected="isSelected" v-bind="$attrs" :icon="icon" @click="setSelected(name)">
        <slot/>
    </a-nav-link>
    <a-button v-else-if="buttonStyle == 'button'" :disabled="isSelected" v-bind="$attrs" :icon="icon" @click="setSelected(name)">
        <slot/>
    </a-button>
</template>

<script setup lang="ts">
import { Ref } from 'vue';

const props = defineProps({
    name: [String, Number],
    icon: String,
});

const buttonStyle = inject('buttonStyle', 'button');
const selected = inject<Ref<string>>('selected');
const setSelected = inject<(name) => void>('setSelected');

const isSelected = computed(() => props.name === selected.value);
</script>