<template>
    <flex :class="classes" gap="2">
        <span class="text-3xl self-start">
            <font-awesome-icon :icon="icon"/>
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
    title: String,
    desc: String,
});

const icons = {
    success: 'circle-check',
    danger: 'triangle-exclamation',
    info: 'circle-info',
    warning: 'circle-exclamation',
};

const icon = computed(() => icons[props.color]);

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
    color: var(--success-color);
    background-color: #1e3b28;
    border-color: var(--success-color);
}

.alert-danger {
    color: var(--danger-color);
    background-color: #382125;
    border-color: var(--danger-color);
}

.alert-info {
    color: var(--info-color);
    background-color: #1e293f;
    border-color: var(--info-color);

}

.alert-warning {
    color: var(--warning-color);
    background-color: #383925;
    border-color: var(--warning-color);
}
</style>