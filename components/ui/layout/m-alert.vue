<template>
    <component :is="to ? NuxtLink : 'div'" :class="classes" :to="to">
        <m-flex gap="2" class="w-full">
            <span v-if="currIcon" class="text-3xl self-start">
                <m-icon :icon="currIcon"/>
            </span>

            <m-flex column>
                <strong v-if="title" class="flex gap-1 text-lg">
                    {{title}}
                </strong>
                <slot>{{desc}}</slot>
            </m-flex>
            <slot name="attach"/>
        </m-flex>
    </component>
</template>

<script setup lang="ts">
import RiCheckboxCircleFill from '~icons/ri/checkbox-circle-fill';
import MdiAlert from '~icons/mdi/alert';
import MdiInformation from '~icons/mdi/information';
import RiWarningFill from '~icons/ri/error-warning-fill';


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
    success: RiCheckboxCircleFill,
    danger: MdiAlert,
    info: MdiInformation,
    warning: RiWarningFill,
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