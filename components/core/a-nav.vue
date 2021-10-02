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
    import { provide } from '@nuxtjs/composition-api';

    const props = defineProps({
        side: Boolean,
        route: String,
        padding: {
            default: 2,
            type: [String, Number]
        }
    });

    provide('route', props.route);
    provide('side', props.route);
</script>