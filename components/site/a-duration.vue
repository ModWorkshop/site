<template>
    <flex>
        <a-select v-model="duration" :options="durations" v-bind="$attrs" :disabled="disabled"/>
        <a-input 
            v-if="duration == 'custom'"
            v-model="customDate"
            type="date"
            :label="$t('date')"
            :disabled="disabled"
            @update:model-value="val => $emit('update:modelValue', val)"
        />
        <a-input v-else-if="duration" v-model="durationCount" :label="$t('count')" :disabled="disabled"/>
    </flex>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon';

const props = defineProps({
    modelValue: String,
    disabled: Boolean
});

const now = DateTime.now();
const durations = [
    { name: 'Days', id: 'days' },
    { name: 'Weeks', id: 'weeks' },
    { name: 'Months', id: 'months' },
    { name: 'Years', id: 'years' },
    { name: 'Permanent', id: '' },
    { name: 'Custom', id: 'custom' },
];

const duration = ref('days');
const durationCount = ref(1);
const customDate = computed(() => {
    try {
        return DateTime.fromISO(props.modelValue).toFormat('yyyy-MM-dd');
    } catch (error) {
        return now.toFormat('yyyy-MM-dd');
    }
});

const emit = defineEmits(['update:modelValue']);

onBeforeMount(() => {
    if (props.modelValue) {
        duration.value = 'custom';
    } else if (props.modelValue === null) {
        duration.value = '';
    }
});

watch([duration, durationCount, customDate], () => {
    if (duration.value !== 'custom') {
        emit('update:modelValue', duration.value ? now.plus({ [duration.value]: durationCount.value }) : null);
    }
}, { immediate: true });
</script>