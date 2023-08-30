<template>
    <a-input>
        <VDropdown
            v-if="classic"
            :shown="shown"
            class="max-w-full"
            distance="0"
            :placement="placement"
            auto-size="max" 
            auto-boundary-max-size
            handle-resize
            :overflow-padding="64"
            @update:shown="(val: boolean) => $emit('update:shown', val)"
        >
            <slot/>
            <template #popper>
                <slot name="popper"/>
            </template>
        </VDropdown>
        <flex v-else class="items-center">
            <slot/>
            <VDropdown 
                v-if="!disabled"
                :shown="shown"
                distance="0"
                auto-boundary-max-size
                handle-resize
                :overflow-padding="16"
                @update:shown="(val: boolean) => $emit('update:shown', val)"
            >
                <a-button>
                    <i-mdi-plus-thick class="text-sm"/>
                </a-button>
                <template #popper>
                    <slot name="popper"/>
                </template>
            </VDropdown>
        </flex>
    </a-input>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
    shown: boolean,
    disabled?: boolean,
    placement?: string,
    classic?: boolean
}>(), {
    classic: true,
    placement: 'bottom-start'
});

defineEmits<{
    ( e: 'update:shown', val: boolean ): void
}>();
</script>