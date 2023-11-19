<template>
    <a-input>
        <mws-dropdown v-if="classic" v-model:open="shown" class="max-w-full" dropdown-class="!overflow-hidden">
            <slot/>
            <template #content>
                <slot name="content"/>
            </template>
        </mws-dropdown>
        <flex v-else class="items-center">
            <slot/>
            <mws-dropdown v-if="!disabled" v-model:open="shown" dropdown-class="!overflow-hidden">
                <a-button>
                    <i-mdi-plus-thick class="text-sm"/>
                </a-button>
                <template #content>
                    <slot name="content"/>
                </template>
            </mws-dropdown>
        </flex>
    </a-input>
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