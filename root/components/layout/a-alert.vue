<template>
    <component :is="to ? NuxtLink : 'div'" :class="classes" :to="to">
        <span v-if="currIcon" class="text-3xl self-start">
            <a-icon :icon="currIcon"/>
        </span>
        <flex column gap="2">
            <strong v-if="title" class="text-xl">{{title}}</strong>
            <slot>{{desc}}</slot>
        </flex>
        <slot name="attach"/>
    </component>
</template>

<script setup lang="ts">
const NuxtLink = resolveComponent('NuxtLink');

const props = defineProps({
    color: { type: String, default: 'info' },
    icon: {
        default: true,
        type: [String, Boolean]
    },
    to: String,
    title: String,
    desc: String,
});

const icons = {
    success: 'mdi:check-circle',
    danger: 'mdi:alert',
    info: 'mdi:information',
    warning: 'mdi:alert-circle',
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
        'flex': true,
        'gap-2': true,
        'text-body': true,
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