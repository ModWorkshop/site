<template>
    <a-input>
        <VDropdown 
			v-model:shown="dropdownOpen"
			class="max-w-full"
			distance="0"
			auto-size
			auto-boundary-max-size
			handle-resize
			:overflow-padding="16"
		>
            <flex gap="2" :class="{'items-center': true, 'mw-input': true, 'max-w-full': true, 'mw-input-invalid': showInvalid}">
				<flex class="overflow-hidden">
					<template v-if="multiple && shownOptions.length">
						<slot v-for="option of shownOptions" :key="optionValue(option)" name="option" :option="option">
							<a-tag :color="optionColor(option)" style="padding: 0.3rem 0.5rem;">
								<font-awesome-icon v-if="optionEnabled(option)" class="cursor-pointer text-md" icon="circle-xmark" @click="deselectOption(option)"/> {{optionName(option)}}
							</a-tag>
						</slot>
						<template v-if="shownOptions.length < selected.length">
							<a-tag>+{{selected.length - shownOptions.length}}</a-tag>
						</template>
					</template>
					<slot v-else-if="selectedOption" name="option" :option="selectedOption">
						<span class="selection">{{optionName(selectedOption)}}</span>
					</slot>
					<span v-else class="selection text-secondary">{{placeholder}}</span>
				</flex>
				<flex class="ml-auto" gap="2">
					<font-awesome-icon v-if="clearable && (selectedOptions.length || selectedOption)" icon="xmark" @click.prevent="clearAll"/>
					<font-awesome-icon icon="angle-down" class="arrow" :style="{ transform: `rotate(${dropdownOpen ? 180 : 0}deg)` }"/>
				</flex>
			</flex>
            <template #popper>
                <flex column>
					<a-input v-if="filterable" v-model="search" class="flex-grow"/>
					<a-dropdown-item v-for="option of filtered" :key="optionValue(option)" :style="{ opacity: optionSelected(option) ? 0.5 : 1 }" @click="toggleOption(option)">
						<slot name="list-option" :option="option">{{optionName(option)}}</slot>
					</a-dropdown-item>
                </flex>
            </template>
        </VDropdown>
    </a-input>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';

const props = withDefaults(defineProps<{
	url?: string,
    modelValue: any,
	options?: any[],
    clearable?: boolean,
    valueBy?: false|string|((option) => string),
    textBy?: false|string|((option) => string),
    colorBy?: false|string|((option) => string),
    enabledBy?: string|((option) => boolean),
    disabled?: boolean,
    filterSelected?: boolean,
    multiple?: boolean,
    filterable?: boolean,
    placeholder?: string,
	max?: string|number,
	maxShown?: string|number
}>(), {
    valueBy: 'id',
    textBy: 'name',
    filterSelected: false,
    placeholder: 'Select...',
    clearable: true,
    filterable: true,
});

const search = ref('');
const searchDebounced = refThrottled(search, props.url ? 500 : 10);
const dropdownOpen = ref(false);
const showInvalid = ref(false);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string|number|any[]): void,
	(e: 'fetched', options: any[]): void
}>();

const { data: asyncOptions, refresh } = await useFetchMany(props.url ?? 'a', { 
	immediate: !!props.url,
	params: reactive({
		query: searchDebounced
	}),
	initialCache: true
});

const opts = computed(() => props.options ?? asyncOptions.value?.data);
const selected = computed(() => typeof props.modelValue == 'object' ? props.modelValue : [props.modelValue]);
const selectedMax = computed(() => selected.value.length >= props.max);

function defaultBy(option, propName) {
    if (typeof props[propName] === 'string') {
        if (typeof option == 'object') {
            return option[props[propName]];
        } else {
            return option;
        }
    } else {
        return option;
    }
}

function optionEnabled(option) {
    if (typeof props.enabledBy == 'function') {
        return props.enabledBy(option);
    } else if (props.enabledBy) {
        return defaultBy(option, 'enabledBy');
    } else {
        return true;
    }
}

function optionName(option) {
    if(typeof props.textBy == 'function') {
        return props.textBy(option);
    } else {
        return defaultBy(option, 'textBy');
    }
}

function optionValue(option) {
    if(typeof props.valueBy == 'function') {
        return props.valueBy(option);
    } else {
        return defaultBy(option, 'valueBy');
    }
}

function optionColor(option) {
    if(typeof props.colorBy == 'function') {
        return props.colorBy(option);
    } else if (props.colorBy) {
        return defaultBy(option, 'colorBy');
    }
}

watch(searchDebounced, async () => {
	if (props.url) {
		await refresh();
	}
});

const filtered = computed(() => {
    const searchLower = searchDebounced.value.toLowerCase();
    let options = opts.value;

	if (!options) {
		return [];
	}

    if (searchLower) {
        options = options.filter(option => optionName(option).toLowerCase().match(searchLower));
    }

    if (props.filterSelected) {
        if (props.multiple && typeof props.modelValue == 'object') {
            options = options.filter(option => optionEnabled(option) && !selected.value.includes(optionValue(option)));
        } else {
            options = options.filter(option => optionEnabled(option) && props.modelValue === optionValue(option));
        }
    }

    return options;
});

function clearAll() {
    if (props.multiple && typeof props.modelValue == 'object') {
        for (const option of opts.value) {
            const value = optionValue(option);
            if (optionEnabled(option)) {
                remove(props.modelValue, value);
            }
        }

        emit('update:modelValue', props.modelValue);
    } else {
        deselectOption(selected.value);
    }
}

const selectedOptions = computed(() => {
	if (opts.value) {
		return opts.value.filter(option => {
			if (selected.value && selected.value.includes) {
				return selected.value.includes(optionValue(option));
			} else {
				return false;
			}
		});
	} else {
		return [];
	}
});

const shownOptions = computed(() => selectedOptions.value.filter((_, i) => (i + 1) <= props.maxShown));

const selectedOption = computed(() => {
	if (opts.value) {
		return opts.value.find(option => props.modelValue === optionValue(option));
	} else {
		return null;
	}
});

function optionSelected(option) {
	if (selectedOptions.value) {
		return selectedOptions.value.includes(option);
	}
}

function toggleOption(option) {
    if (optionSelected(option)) {
        deselectOption(option);
    } else {
        selectOption(option);
    }
}

function selectOption(option) {
    const value = optionValue(option);
    if (props.multiple && typeof props.modelValue == 'object') {
		if (selectedMax.value) {
			showInvalid.value = true;
			setTimeout(() => {
				showInvalid.value = false;
			}, 500);
			return;
		}
        if (!selected.value.includes(value)) {
            props.modelValue.push(value);
        }
        emit('update:modelValue', props.modelValue);
    } else {
        emit('update:modelValue', value);
        dropdownOpen.value = false;
    }
}

function deselectOption(item) {
    const value = optionValue(item);

    if (props.multiple && typeof props.modelValue == 'object') {
        remove(props.modelValue, value);
        emit('update:modelValue', props.modelValue);
    } else {
        emit('update:modelValue', undefined);
        dropdownOpen.value = false;
    } 
}
</script>

<style scoped>

.arrow {
    transition: transform 0.25s;
}

.selection {
    user-select: none;
}
</style>