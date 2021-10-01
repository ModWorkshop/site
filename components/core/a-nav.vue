<template>
    <flex :column="!side" :gap="side ? 8 : 1">
        <flex class="nav" :column="side">
            <slot/>
        </flex>
        <div :class="{'tab-panels': true, [`px-${padding}`]: padding !== 0, 'flex-grow': side}">
            <slot name="content"/>
        </div>
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