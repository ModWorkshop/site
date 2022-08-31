<template>
    <flex :class="classes" gap="3">
        <span class="text-3xl">
            <font-awesome-icon :icon="icon"/>
        </span>
        <flex column class="whitespace-pre">
            <strong v-if="title" class="text-xl">{{title}}</strong>
            <slot><span class="my-auto">{{desc}}</span></slot>
        </flex>
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
    };
});
</script>

<style scoped>
.alert {
    background-color: var(--content-bg-color);
    border-radius: var(--border-radius);
    border-left: solid 4px transparent;
}
.alert-success {
    color: var(--success-color);
    background-color: rgb(68, 0, 255 / 10%);
    border-color: var(--success-color);
}

.alert-danger {
    color: var(--danger-color);
    background-color: rgb(255 0 0 / 10%);
    border-color: var(--danger-color);
}

.alert-info {
    color: var(--info-color);
    background-color: rgb(0 80 255 / 10%);
    border-color: var(--info-color);

}

.alert-warning {
    color: var(--warning-color);
    background-color: rgb(255, 234, 0 / 10%);
    border-color: var(--warning-color);
}
</style>