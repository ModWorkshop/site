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
        <a-input v-else-if="duration" v-model="durationCount" :label="$t('duration_count')" :disabled="disabled"/>
    </flex>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    modelValue: String,
    disabled: Boolean
});

const { t } = useI18n();

const now = DateTime.now();
const durations = [
    { name: t('days'), id: 'days' },
    { name: t('weeks'), id: 'weeks' },
    { name: t('months'), id: 'months' },
    { name: t('years'), id: 'years' },
    { name: t('forever'), id: '' },
    { name: t('custom_duration'), id: 'custom' },
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
        emit('update:modelValue', duration.value ? now.plus({ [duration.value]: durationCount.value }).toString() : null);
    }
}, { immediate: true });
</script>