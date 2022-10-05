<template>
    <flex :class="classes" gap="2">
        <span v-if="currIcon" class="text-3xl self-start">
            <font-awesome-icon :icon="currIcon"/>
        </span>
        <flex column>
            <strong v-if="title" class="text-xl">{{title}}</strong>
            <slot>{{desc}}</slot>
        </flex>
        <slot name="attach"/>
    </flex>
</template>

<script setup lang="ts">
const props = defineProps({
    color: { type: String, default: 'info' },
    icon: {
        default: true,
        type: [String, Boolean]
    },
    title: String,
    desc: String,
});

const icons = {
    success: 'circle-check',
    danger: 'triangle-exclamation',
    info: 'circle-info',
    warning: 'circle-exclamation',
};

const currIcon = computed(() =>  {
    if (typeof props.icon == 'string') {
        return props.icon;
    } else if (props.icon !== false) {
        return icons[props.color];
    }
});

const classes = computed(() => {
    return {
        alert: true,
        'content-top': true,
        'p-4': true,
        'alert-success': props.color == 'success',
        'alert-danger': props.color == 'danger',
        'alert-info': props.color == 'info',
        'alert-warning': props.color == 'warning',
        'items-center': true
    };
});
</script>

<style scoped>
.alert {
    background-color: var(--content-bg-color);
    border-radius: var(--border-radius);
    border-left: solid 3px transparent;
}
.alert-success {
    background-color: var(--success-bg-color);
    border-color: var(--success-color);
}

.alert-danger {
    background-color: var(--danger-bg-color);
    border-color: var(--danger-color);
}

.alert-info {
    background-color: var(--info-bg-color);
    border-color: var(--info-color);

}

.alert-warning {
    background-color: var(--warning-bg-color);
    border-color: var(--warning-color);
}
</style>