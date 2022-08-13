<template>
    <flex class="alt-bg-color round progress relative">
        <span v-if="text" class="progress-text">{{textToShow}}</span>
        <div :class="progressClasses" :style="{width: `${percentToShow}%`}"/>
    </flex>
</template>

<script setup>
const props = defineProps({
    color: { type: String, default: 'primary' },
    current: [String, Number],
    total: [String, Number],
    percent: Number,
    showText: { type: Boolean, default: true },
    text: String,
    textAsPercent: Boolean,
});

const percentToShow = computed(() => props.percent ?? (100 * (props.current/props.total)));
const textToShow = computed(() => {
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
    height: 2rem;
    transition: width 0.25s ease-in-out;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
</style>