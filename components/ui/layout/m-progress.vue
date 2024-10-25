<template>
    <m-flex class="alt-content-bg round progress relative" :style="{height: `${height}px`}">
        <span  class="progress-text ml-auto">{{textToShow}}</span>
        <div :class="progressClasses" :style="{width: `${percentToShow}%`}"/>
    </m-flex>
</template>

<script setup lang="ts">
const { 
    color = 'primary',
    height = 28,
    showText = true,
    text,
    textAsPercent,
    percent,
    current = 0,
    total = 1
} = defineProps<{
    color?: string,
    current?: number,
    total?: number,
    percent?: number,
    height?: number,
    showText?: boolean,
    text?: string,
    textAsPercent?: boolean,
}>();

const percentToShow = computed(() => Math.round(percent ?? (100 * (current/total))));
const textToShow = computed(() => {
    if (!showText) {
        return null;
    }

    if (text) {
        return text;
    }

    if (textAsPercent) {
        return percentToShow.value + '%';
    }

    const c = textAsPercent ? percentToShow : (current ?? percentToShow.value);
    const t = textAsPercent ? 100 : (total ?? 100);

    return `${c}/${t}`;
});

const progressClasses = computed(() => {
    return {
        'progress-bar': true,
        round: true,
        [`bg-${color}`]: true
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