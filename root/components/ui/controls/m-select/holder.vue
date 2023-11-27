<template>
    <m-input>
        <m-dropdown v-if="classic" v-model:open="shown" class="max-w-full" dropdown-class="!overflow-hidden">
            <slot/>
            <template #content>
                <slot name="content"/>
            </template>
        </m-dropdown>
        <m-flex v-else class="items-center">
            <slot/>
            <m-dropdown v-if="!disabled" v-model:open="shown" dropdown-class="!overflow-hidden">
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
    classic?: boolean
}>(), {
    classic: true,
    placement: 'bottom-start'
});

const shown = defineModel<boolean>('shown', { local: true, default: false });
</script>