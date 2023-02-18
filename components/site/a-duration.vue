<template>
    <flex>
        <a-input 
            v-model="vm"
            type="date"
            :label="label"
            :disabled="disabled"
            style="resize: none;"
        />
        <flex class="mt-auto mb-1">
            <VDropdown>
                <a-button icon="ic:outline-keyboard-arrow-down">{{ $t('days') }}</a-button>
                <template #popper>
                    <flex column>
                        <a-dropdown-item v-for="i in [...Array(6).keys()]" :key="`day_shortcut_${i}`" @click="bumpDate('days', i+1)">{{i+1}}</a-dropdown-item>
                    </flex>
                </template>
            </VDropdown>
            <VDropdown>
                <a-button icon="ic:outline-keyboard-arrow-down">{{ $t('weeks') }}</a-button>
                <template #popper>
                    <flex column>
                        <a-dropdown-item v-for="i in [...Array(4).keys()]" :key="`day_shortcut_${i}`" @click="bumpDate('weeks', i+1)">{{i+1}}</a-dropdown-item>
                    </flex>
                </template>
            </VDropdown>
            <VDropdown>
                <a-button icon="ic:outline-keyboard-arrow-down">{{ $t('months') }}</a-button>
                <template #popper>
                    <flex column>
                        <a-dropdown-item v-for="i in [...Array(12).keys()]" :key="`day_shortcut_${i}`" @click="bumpDate('months', i+1)">{{i+1}}</a-dropdown-item>
                    </flex>
                </template>
            </VDropdown>
            <a-button @click="$emit('update:modelValue')">{{$t('forever')}}</a-button>
        </flex>
    </flex>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon';

const props = defineProps<{
    modelValue?: string|null,
    label?: string,
    disabled?: boolean
}>();

const emit = defineEmits(['update:modelValue']);

const vm = computed({
    get() {
        if (props.modelValue) {
            return DateTime.fromISO(props.modelValue).toISODate(); //Why the fuck can't you just accept the regular ISO8601 date???
        } else {
            return null;
        }
    },
    set(val) {
        emit('update:modelValue', val);
    }
});

const now = DateTime.now();

function bumpDate(time, count) {
    vm.value = now.plus({ [time]: count }).toISODate();
}
</script>