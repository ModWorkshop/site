<template>
    <a-input>
        <VDropdown
            v-if="classic"
            v-model:shown="shown"
            class="max-w-full"
            distance="0"
            :placement="placement"
            auto-size
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
                v-model:shown="shown"
                distance="0"
                auto-boundary-max-size
                handle-resize
                :overflow-padding="16"
                @update:shown="(val: boolean) => $emit('update:shown', val)"
            >
                <a-button icon="plus" size="sm"/>
                <template #popper>
                    <slot name="popper"/>
                </template>
            </VDropdown>
        </flex>
    </a-input>
</template>

<script setup lang="ts">
defineProps({
    shown: Boolean,
    disabled: Boolean,
    placement: String,
    classic: {
        default: true,
        type: Boolean
    },
});

defineEmits<{
    ( e: 'update:shown', val: boolean ): void
}>();
</script>