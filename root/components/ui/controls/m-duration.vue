<template>
    <m-flex wrap>
        <m-input 
            v-model="controlVm"
            type="date"
            :label="label"
            :disabled="disabled"
            style="resize: none;"
        />
        <m-flex class="mt-auto mb-1" wrap>
            <m-dropdown>
                <m-button><i-mdi-menu-down/> {{ $t('days') }}</m-button>
                <template #content>
                    <m-flex column>
                        <m-dropdown-item v-for="i in [...Array(6).keys()]" :key="`day_shortcut_${i}`" @click="bumpDate('days', i+1)">{{i+1}}</m-dropdown-item>
                    </m-flex>
                </template>
            </m-dropdown>
            <m-dropdown>
                <m-button><i-mdi-menu-down/> {{ $t('weeks') }}</m-button>
                <template #content>
                    <m-flex column>
                        <m-dropdown-item v-for="i in [...Array(4).keys()]" :key="`day_shortcut_${i}`" @click="bumpDate('weeks', i+1)">{{i+1}}</m-dropdown-item>
                    </m-flex>
                </template>
            </m-dropdown>
            <m-dropdown>
                <m-button><i-mdi-menu-down/> {{ $t('months') }}</m-button>
                <template #content>
                    <m-flex column>
                        <m-dropdown-item v-for="i in [...Array(12).keys()]" :key="`day_shortcut_${i}`" @click="bumpDate('months', i+1)">{{i+1}}</m-dropdown-item>
                    </m-flex>
                </template>
            </m-dropdown>
            <m-button @click="$emit('update:modelValue')">{{$t('forever')}}</m-button>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon';

const props = defineProps<{
    modelValue?: string|null,
    label?: string,
    disabled?: boolean
}>();

const emit = defineEmits(['update:modelValue']);

const vm = useVModel(props, 'modelValue', emit);

const controlVm = computed({
    get() {
        if (props.modelValue) {
            return DateTime.fromISO(props.modelValue).toISODate(); //Why the fuck can't you just accept the regular ISO8601 date???
        } else {
            return null;
        }
    },
    set(val) {
        const now = DateTime.now();
        vm.value = val ? DateTime.fromISO(val).set({ hour: now.hour, minute: now.minute }).toISO() : null;
    }
});


function bumpDate(time, count) {
    controlVm.value = DateTime.now().plus({ [time]: count }).toISO();
}
</script>