<template>
    <m-input class="relative" :required="required" :disabled="disabled">
        <input class="kinda-hidden" :value="hasValue ? 1 : undefined" :required="required">
        <template #label>
            <slot name="label"/>
        </template>
        <m-dropdown 
            v-if="classic"
            v-model:open="shown"
            :disabled="disabled"
            :class="{'max-w-full': true, 'dropdown-disabled': disabled}"
            dropdown-class="!overflow-hidden"
        >
            <slot/>
            <template #content>
                <slot name="content"/>
            </template>
        </m-dropdown>
        <m-flex v-else class="items-center">
            <slot/>
            <m-dropdown 
                v-if="!disabled"
                v-model:open="shown"
                :disabled="disabled"
                dropdown-class="!overflow-hidden"
                :class="{'max-w-full': true, 'dropdown-disabled': disabled}"
            >
                <m-button>
                    <i-mdi-plus-thick class="text-sm"/>
                </m-button>
                <template #content>
                    <slot name="content"/>
                </template>
            </m-dropdown>
        </m-flex>
    </m-input>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
    disabled?: boolean,
    placement?: string,
    classic?: boolean,
    required?: boolean,
    hasValue?: boolean,
}>(), {
    classic: true,
    placement: 'bottom-start'
});

const shown = defineModel<boolean>('shown', { local: true, default: false });
</script>

<style scoped>
.kinda-hidden {
    opacity: 0;
    width: 100%;
    z-index: -999;
    transform: translateY(42px);
    position: absolute;
}

.dropdown-disabled {
    opacity: 0.4;
}
</style>