<template>
    <m-flex class="alt-content-bg round progress relative" :style="{height: `${height}px`}">
        <span v-if="textToShow" class="progress-text">{{textToShow}}</span>
        <div :class="progressClasses" :style="{width: `${percentToShow}%`}"/>
    </m-flex>
</template>

<script setup lang="ts">
const props = defineProps({
    color: { type: String, default: 'primary' },
    current: [String, Number],
    total: [String, Number],
    percent: Number,
    height: { type: Number, default: 28 },
    showText: { type: Boolean, default: true },
    text: String,
    textAsPercent: Boolean,
});

const percentToShow = computed(() => props.percent ?? (100 * (props.current/props.total)));
const textToShow = computed(() => {
    if (!props.showText) {
        return null;
    }

    if (props.text) {
        return props.text;
    }

    const current = props.textAsPercent ? percentToShow : (props.current ?? percentToShow.value);
    const total = props.textAsPercent ? 100 : (props.total ?? 100);

    return `${current}/${total}`;
});

const progressClasses = computed(() => {
    return {
        'progress-bar': true,
        round: true,
        [`bg-${props.color}`]: true
    };
});

</script>

<style scoped>
.progress-bar {
    transition: width 0.25s ease-in-out;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    text-align: center;
    transform: translate(-50%, -50%);
}
</style>