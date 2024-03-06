<template>
    <div>
        <PopoverRoot v-if="type == 'dropdown'" v-model:open="open">
            <PopoverTrigger aria-label="Update dimensions" as="span" v-bind="$attrs">
                <slot/>
            </PopoverTrigger>
            <PopoverPortal>
                <PopoverContent 
                    :side="side"
                    :align="align"
                    :class="computedClass"
                    :side-offset="2"
                    :trap-focus="trapFocus"
                    update-position-strategy="optimized"
                >
                    <slot name="content"/>
                    <PopoverArrow class="m-dropdown-arrow"/>
                </PopoverContent>
            </PopoverPortal>
        </PopoverRoot>
        <TooltipProvider v-else-if="type == 'tooltip'" :delay-duration="toolTipDelay">
            <TooltipRoot v-model:open="open">
                <TooltipTrigger aria-label="Update dimensions" as="span" v-bind="$attrs">
                    <slot/>
                </TooltipTrigger>
                <TooltipPortal v-if="!disabled">
                    <TooltipContent :side="side" :align="align" :class="computedClass" :side-offset="2" :collision-padding="32" :avoid-collisions="true">
                        <slot name="content"/>
                        <TooltipArrow class="m-dropdown-arrow"/>
                    </TooltipContent>
                </TooltipPortal>
            </TooltipRoot>
        </TooltipProvider>
    </div>
</template>
  
<script setup lang="ts">
import { PopoverContent, PopoverPortal, PopoverRoot, PopoverTrigger, PopoverArrow } from 'radix-vue';
import { TooltipPortal, TooltipArrow, TooltipContent, TooltipProvider, TooltipRoot, TooltipTrigger } from 'radix-vue';

const { 
    side = 'bottom',
    align = 'start',
    dropdownClass,
    trapFocus = true,
    disabled = false,
    type = 'dropdown',
    toolTipDelay
} = defineProps<{
    side?: "right" | "left" | "bottom" | "top";
    align?: "start" | "center" | "end";
    type?: 'dropdown' | 'tooltip'
    dropdownClass?: any;
    trapFocus?: boolean;
    disabled?: boolean;
    toolTipDelay?: number;
}>();

const open = defineModel<boolean>('open', { local: true, default: false });

const computedClass = computed(() => {
    if (Array.isArray(dropdownClass)) {
        dropdownClass.push('m-dropdown');
    } else if (typeof dropdownClass === 'object') {
        dropdownClass['m-dropdown'] = true;
    } else if (typeof dropdownClass === 'string') {
        return ['m-dropdown', dropdownClass];
    } else {
        return 'm-dropdown';
    }
});

// A little hack to add disabling
watch(open, () => {
    if (disabled && open.value) {
        open.value = false;
    }
});
</script>

<style>
.m-dropdown {
    background: var(--dropdown-bg);
    color: var(--text-color);
    border-radius: var(--border-radius);
    border: 1px solid var(--input-border-color);
    max-width: 400px;
    max-height: 450px;
    color: var(--text-color);
    z-index: 9999;
    animation-duration: 800ms;
    animation-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
    will-change: transform, opacity;
    overflow: auto;
}

.m-dropdown[data-side='top'] {
    animation-name: slideDownAndFade;
}
.m-dropdown[data-side='right'] {
    animation-name: slideLeftAndFade;
}
.m-dropdown[data-side='bottom']  {
    animation-name: slideUpAndFade;
}
.m-dropdown[data-side='left']{
    animation-name: slideRightAndFade;
}

.m-dropdown-arrow {
    fill: var(--input-border-color);
}

@keyframes slideUpAndFade {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideRightAndFade {
    from {
        opacity: 0;
        transform: translateX(-8px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideDownAndFade {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideLeftAndFade {
    from {
        opacity: 0;
        transform: translateX(8px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>