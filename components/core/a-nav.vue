<template>
    <flex :column="!side" :gap="side ? 8 : 1">
        <flex class="nav" :column="side">
            <slot/>
        </flex>
        <flex column grow gap="3">
            <slot name="content"/>
        </flex>
    </flex>
</template>

<script setup>
    const props = defineProps({
        side: Boolean,
        root: String,
        padding: {
            default: 2,
            type: [String, Number]
        }
    });

    provide('root', props.root);
    provide('side', props.side);
</script>