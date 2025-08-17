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
            <m-button @click="vm = null">{{$t('forever')}}</m-button>
        </m-flex>
    </m-flex>
</template>

<script setup lang="ts">
import { add, formatISO, parseISO, set } from 'date-fns';

defineProps<{
    label?: string,
    disabled?: boolean
}>();

const vm = defineModel<string|null>();

const controlVm = computed({
    get() {
        if (vm.value) {
            return formatISO(parseISO(vm.value), { representation: 'date' });
        } else {
            return null;
        }
    },
    set(val: Date|string) {
        if (typeof val == "string") {
            vm.value = val;
            return;
        }

        const now = new Date();
        vm.value = val ? set(val, { hours: now.getHours(), minutes: now.getMinutes() }).toISOString() : null;
    }
});


function bumpDate(time, count) {
    controlVm.value = add(new Date(), { [time]: count });
}
</script>