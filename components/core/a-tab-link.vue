<template>
    <a
        role="tab"
        :id="`${tabName}-tab-link`"
        :class="{'tab-link': true, 'tab-link-side': side, selected: tabState.current == tabName}"
        :data-selected="tabState.current == tabName"
        :tabindex="tabState.current == tabName ? 0 : -1"
        :aria-selected="tabState.current == tabName"
        href="#"
        :aria-controls="`${tabName}-tab-panel`">
        {{tabTitle}}
    </a>
</template>

<script setup>
defineProps({
    tabTitle: String,
    tabName: String
});

const tabState = inject('tabState');
const side = inject('side');
</script>

<style>
    .tab-link {
        font-size: 1.125rem;
        border-radius: 4px;
        padding: 0.75rem 2rem;
        color: var(--secondary-text-color);
    }

    .tab-link:hover {
        cursor: pointer;
    }

    .tab-link.selected {
        background-color: var(--tab-selected-color);
        color: var(--primary-color);
    }

    .tab-link-side {
        padding: 0.5rem 4rem 0.5rem 0.75rem;
        min-width: 200px;
    }
</style>