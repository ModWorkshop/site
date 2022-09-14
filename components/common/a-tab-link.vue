<template>
    <a
        :id="`${tabName}-tab-link`"
        role="tab"
        :class="{'nav-link': true, 'nav-link-side': side, selected: tabState.current == tabName}"
        :data-selected="tabState.current == tabName"
        :tabindex="tabState.current == tabName ? 0 : -1"
        :aria-selected="tabState.current == tabName"
        href="#"
        :aria-controls="`${tabName}-tab-panel`"
    >
        {{tabTitle}}
    </a>
</template>

<script setup>
defineProps({
    tabTitle: String,
    tabName: String
});

const tabState = inject('tabState');
const side = inject('side', false);
</script>