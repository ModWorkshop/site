<template>
    <flex :class="classes" gap="3">
        <span class="text-3xl">
            <font-awesome-icon :icon="icon"/>
        </span>
        <flex column>
            <strong v-if="title" class="text-xl">{{title}}</strong>
            <slot>{{desc}}</slot>
        </flex>
    </flex>
</template>

<script setup lang="ts">
const props = defineProps({
    color: String,
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
        'content-top': true,
        round: true,
        'p-4': true,
        'alert-success': props.color == 'success',
        'alert-danger': props.color == 'danger',
        'alert-info': props.color == 'info',
        'alert-warning': props.color == 'warning',
    };
});
</script>

<style scoped>
.alert-success {
    background-color: #22c31f;
    color: #000;
}

.alert-danger {
    background-color: #a12121;
}

.alert-info {
    background-color: #40474f;
}

.alert-warning {
    background-color: #d9cd40;
    color: #000;
}
</style>