<template>
    <a-select-holder v-model:shown="dropdownOpen" :classic="classic" :disabled="disabled">
        <flex :gap="classic ? 2 : 0" :class="{'items-center': true, 'mw-input': classic, 'max-w-full': true, 'mw-input-invalid': showInvalid}">
            <flex wrap class="overflow-hidden">
                <template v-if="multiple && shownOptions.length">
                    <slot v-for="option of shownOptions" :key="optionValue(option)" name="option" :option="option">
                        <slot name="any-option" :option="option">
                            <a-tag :color="optionColor(option)" :style="{padding: classic ? '0.3rem 0.5rem;' : undefined}">
                                <i-mdi-close-thick v-if="!disabled && optionEnabled(option)" class="cursor-pointer text-md" @click="deselectOption(option)"/> {{optionName(option)}}
                            </a-tag>
                        </slot>
                    </slot>
                    <template v-if="shownOptions.length < selected.length">
                        <a-tag>+{{selected.length - shownOptions.length}}</a-tag>
                    </template>
                </template>
                <slot v-else-if="selectedOption" name="option" :option="selectedOption">
                    <slot name="any-option" :option="selectedOption">
                        <span class="selection">{{optionName(selectedOption)}}</span>
                    </slot>
                </slot>
                <span v-else class="selection text-secondary">
                    <slot name="placeholder">{{disabled ? $t('none') : placeholder ?? $t('select_placeholder')}}</slot>
                </span>
            </flex>
            <flex class="ml-auto" gap="2">
                <i-mdi-close v-if="compClearable" @click.stop="clearAll"/>
                <i-mdi-menu-down v-if="classic" class="arrow" :style="{ transform: `rotate(${dropdownOpen ? 180 : 0}deg)` }"/>
            </flex>
        </flex>
        <template #popper>
            <flex column :class="listClass" style="min-width: 200px">
                <a-input v-if="compFilterable" v-model="search" class="flex-grow"/>
                <flex column class="overflow-auto">
                    <a-dropdown-item 
                        v-for="option of filtered"
                        :key="optionValue(option)"
                        :disabled="!props.multiple && !props.clearable && optionSelected(option)"
                        :style="{ opacity: optionSelected(option) ? 0.5 : 1 }"
                        @click="toggleOption(option)"
                    >
                        <slot name="list-option" :option="option">
                            <slot name="any-option" :option="option">
                                <a-tag v-if="listTags" :color="option.color">{{ optionName(option) }}</a-tag>
                                <template v-else>
                                    {{ optionName(option) }}
                                </template>
                            </slot>
                        </slot>
                    </a-dropdown-item>
                </flex>
            </flex>
        </template>
    </a-select-holder>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';

const props = withDefaults(defineProps<{
	url?: string,
    fetchParams?: Record<string, any>,
    modelValue: any,
    default?: any,
	options?: any[],
    clearable?: boolean,
    classic?: boolean,
    valueBy?: false|string|((option) => string),
    textBy?: false|string|((option) => string),
    colorBy?: false|string|((option) => string),
    enabledBy?: string|((option) => boolean),
    beforeSelect?: ((value, clbk) => boolean),
    disabled?: boolean,
    filterSelected?: boolean,
    multiple?: boolean,
    filterable?: boolean,
    placeholder?: string,
	max?: string|number,
	maxShown?: string|number,
    listClass?: string|string[],
    listTags?: boolean,
    postFetchFilter?: boolean
}>(), {
    valueBy: 'id',
    textBy: 'name',
    filterable: true,
    filterSelected: false,
    postFetchFilter: false,
    classic: true,
    listTags: false
});

const search = ref('');
const searchDebounced = refThrottled(search, props.url ? 500 : 10);
const dropdownOpen = ref(false);
const showInvalid = ref(false);

const emit = defineEmits<{
    (e: 'update:modelValue', value: any|any[]): void,
	(e: 'fetched', options: any[]): void
}>();

const { data: asyncOptions, refresh } = await useFetchMany(props.url ?? 'a', { 
	immediate: !!props.url,
	params: reactive({
		query: searchDebounced,
        ...props.fetchParams
	}),
});

// Only necessary to retrieve the v-model that may not be contained in asyncOptions
// Example: user query parameter to prefill a user
const { data: fetchedVModel } = await useFetchData((props.url && props.modelValue) ? `${props.url}/${props.modelValue}` : 'a', {
    immediate: !!(props.url && props.modelValue)
});

// The options of the select that are actually shown
// This also handles selected value that may not be in options
const opts = computed(() => {
    if (props.options) {
        return props.options;
    } else if (typeof(asyncOptions.value?.data) == 'object') {
        const fethcedOptionValue = fetchedVModel.value ? optionValue(fetchedVModel.value) : null;
        // Check if it exists at all or was already added
        if (!fethcedOptionValue || asyncOptions.value.data.find(option => optionValue(option) === fethcedOptionValue)) {
            return asyncOptions.value.data;
        } else {
            return [fetchedVModel.value, ...asyncOptions.value.data];
        }
    } else if (fetchedVModel.value) {
        return [fetchedVModel.value];
    } else {
        return [];
    }
});
const selectedValue = computed(() => props.modelValue ?? props.default);
const selected = computed<any[]>(() => props.multiple ? selectedValue.value as any[]: [selectedValue.value]);
const selectedMax = computed(() => {
    if (!props.max) {
        return false;
    }
    const max = typeof props.max == 'number' ? props.max : parseInt(props.max);
    return selected.value.length >= max;
});
const compFilterable = computed(() => props.filterable ?? (!!props.url || opts.value && opts.value.length > 10));
const filtered = computed(() => {
    const searchLower = searchDebounced.value.toLowerCase();
    let options = opts.value;

	if (!options) {
		return [];
	}

    options = options.filter(option => {
        if (!optionEnabled(option)) {
            return false;
        }
        if (searchLower && !props.url && !props.postFetchFilter) {
            return optionName(option).toLowerCase().match(searchLower);
        } else {
            return true;
        }
    });

    if (props.filterSelected) {
        if (props.multiple && typeof selectedValue.value == 'object') {
            options = options.filter(option => optionEnabled(option) && !selected.value.includes(optionValue(option)));
        } else {
            options = options.filter(option => optionEnabled(option) && selectedValue.value === optionValue(option));
        }
    }

    options.sort((a, b) => {
        const aSelected = optionSelected(a);
        const bSelected = optionSelected(b);
        if (aSelected && bSelected) {
            return 0;
        } else {
            return aSelected ? 1 : -1;
        }
    });

    return options;
});

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

const shownOptions = computed(() => selectedOptions.value.filter((_, i) => {
    return !props.maxShown || (i + 1) <= (typeof props.maxShown == "number" ? props.maxShown : parseInt(props.maxShown));
}));

const selectedOption = computed(() => {
	if (opts.value) {
		return opts.value.find(option => selectedValue.value === optionValue(option));
	} else {
		return null;
	}
});

const compClearable = computed(() => {    
    return selected.value?.length > 0 && (props.clearable ?? (props.multiple && (selectedOptions.value.length || selectedOption.value)));
});

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

function clearAll() {
    if (props.multiple && typeof selectedValue.value == 'object' && opts.value) {
        for (const option of opts.value) {
            const value = optionValue(option);
            if (optionEnabled(option)) {
                remove(selectedValue.value, value);
            }
        }

        emit('update:modelValue', selectedValue.value);
    } else {
        deselectOption(selected.value);
    }
}

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

// Triggers when selecting an option from the liust
function selectOption(option) {
    const value = optionValue(option);
    if (props.multiple && typeof selectedValue.value == 'object') {
		if (selectedMax.value) {
			showInvalid.value = true;
			setTimeout(() => {
				showInvalid.value = false;
			}, 500);
			return;
		}
        
        const set = () => {
            if (!selected.value.includes(value)) {
                selectedValue.value.push(value);
            }

            emit('update:modelValue', selectedValue.value);
        };
        
        //Allow for the interception of options before they're selected
        if (!props.beforeSelect) {
            set();
        } else {
            props.beforeSelect(option, set);
        }
    } else {
        const set = () => {
            dropdownOpen.value = false;
            emit('update:modelValue', value);
        };

        if (!props.beforeSelect) {
            set();
        } else {
            props.beforeSelect(option, set);
        }
    }
}

function deselectOption(item) {
    const value = optionValue(item);

    if (props.multiple && typeof selectedValue == 'object') {
        remove(selectedValue.value, value);
        emit('update:modelValue', selectedValue.value);
    } else if (props.clearable) {
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