<template>
	<PopoverRoot v-if="type == 'dropdown'" v-model:open="open">
		<PopoverTrigger aria-label="Update dimensions" as="span" v-bind="$attrs">
			<slot/>
		</PopoverTrigger>
		<PopoverPortal>
			<PopoverContent
				force-mount
				:side="side"
				:align="align"
				:side-offset="2"
				:trap-focus="trapFocus"
				class="z-50"
				update-position-strategy="optimized"
				@click="onClickContent"
			>
				<AnimatePresence>
					<Motion
					v-if="open"
					:class="computedClass"
					:initial="{ opacity: 0, translateY: -8 }"
					:animate="{ opacity: 1, translateY: 0 }"
					:exit="{ opacity: 0, translateY: -8 }"
					:transition="{ duration: 0.2, ease: 'backInOut' }"
					>
						<slot name="content"/>
						<PopoverArrow class="m-dropdown-arrow"/>
					</Motion>
				</AnimatePresence>
			</PopoverContent>
		</PopoverPortal>
	</PopoverRoot>
	<TooltipProvider v-else-if="type == 'tooltip'" :delay-duration="toolTipDelay">
		<TooltipRoot v-model:open="open">
			<TooltipTrigger aria-label="Update dimensions" class="inline-flex" as="span" v-bind="$attrs">
				<slot/>
			</TooltipTrigger>
			<TooltipPortal v-if="!disabled">
				<TooltipContent
					:side="side"
					:align="align"
					class="z-50"
					force-mount
					:side-offset="2"
					:collision-padding="32"
					:avoid-collisions="true"
				>
					<AnimatePresence>
						<Motion
						v-if="open"
						:class="computedClass"
						:initial="{ opacity: 0, translateY: -8 }"
						:animate="{ opacity: 1, translateY: 0 }"
						:exit="{ opacity: 0, translateY: -8 }"
						:transition="{ duration: 0.2, ease: 'backInOut' }"
						>
							<slot name="content"/>
							<PopoverArrow class="m-dropdown-arrow"/>
						</Motion>
					</AnimatePresence>
				</TooltipContent>
			</TooltipPortal>
		</TooltipRoot>
	</TooltipProvider>
</template>

<script setup lang="ts">
import { PopoverContent, PopoverPortal, PopoverRoot, PopoverTrigger, PopoverArrow } from 'reka-ui';
import { TooltipPortal, TooltipArrow, TooltipContent, TooltipProvider, TooltipRoot, TooltipTrigger } from 'reka-ui';
import { AnimatePresence, Motion } from 'motion-v';

const {
	side = 'bottom',
	align = 'start',
	dropdownClass,
	trapFocus = true,
	disabled = false,
	type = 'dropdown',
	toolTipDelay,
	closeOnClick = true,
	padding = 3
} = defineProps<{
	side?: 'right' | 'left' | 'bottom' | 'top';
	align?: 'start' | 'center' | 'end';
	type?: 'dropdown' | 'tooltip';
	dropdownClass?: any;
	trapFocus?: boolean;
	disabled?: boolean;
	toolTipDelay?: number;
	closeOnClick?: boolean;
	padding?: number | string;
}>();

const open = defineModel<boolean>('open', { default: false });

const defaultClasses = computed(() => ['m-dropdown', `p-${padding}`]);

const computedClass = computed(() => {
	if (Array.isArray(dropdownClass)) {
		dropdownClass.push(defaultClasses);
	} else if (typeof dropdownClass === 'object') {
		for (const c of defaultClasses.value) {
			dropdownClass[c] = true;
		}
	} else if (typeof dropdownClass === 'string') {
		return [...defaultClasses.value, dropdownClass];
	} else {
		return defaultClasses.value;
	}
});

function onClickContent() {
	if (closeOnClick) {
		open.value = false;
	}
}

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
	border-radius: var(--content-border-radius);
	box-shadow: var(--content-box-shadow);
	border: 1px solid rgba(120, 120, 120, 0.15);
	max-width: 400px;
	max-height: 450px;
	color: var(--text-color);
	display: flex;
	flex-direction: column;
	gap: 1px;
	z-index: 9999;
	animation-duration: 0.5s;
	animation-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
	will-change: transform, opacity;
	overflow-y: auto;
	/* overflow: auto; */
}

.m-dropdown-arrow {
	fill: var(--dropdown-bg);
}
</style>
