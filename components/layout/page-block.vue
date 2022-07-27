<template>
    <flex :gap="gap" column :class="{'page-block': true, 'page-block-full': size == 'full', 'page-block-med': size == 'med'}">
        <slot/>
    </flex>
</template>

<script setup lang="ts">
    import { FetchError } from 'ohmyfetch';
    import { createError } from 'h3';

    const props = defineProps({
        errorString: String,
        errorStrings: Object,
        error: [Boolean, FetchError],
        gap: {
            type: Number,
            default: 3
        },
        size: {
            type: String,
            default: 'large'
        }
    });

    const error = toRef(props, 'error');

    if (error.value instanceof Error) {
        if (props.errorString) {
            throw createError(props.errorString);
        } else if (props.errorStrings) {
            const code = error.value.response.status;
            throw createError({ statusCode: code, statusMessage: props.errorStrings[code], fatal: true});
        }
    }
</script>

<style scoped>
.page-block {
    border-radius: 4px;
    width: 83%;
}

.page-block-full {
    width: 100%;
}

.page-block-med {
    width: 75%;
}
</style>