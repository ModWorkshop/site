<template>
    <a-input>
        <VDropdown v-model:shown="dropdownOpen" distance="0" auto-size auto-boundary-max-size handle-resize>
            <flex class="items-center mw-input">
				<flex wrap>
					<template v-if="multiple && selectedOptions.length">
						<template v-for="option of selectedOptions" :key="optionValue(option)">
							<slot name="option" :option="option">
								<a-tag :color="optionColor(option)" style="padding: 0.3rem 0.5rem;">
									<font-awesome-icon v-if="optionEnabled(option)" class="cursor-pointer text-md" icon="circle-xmark" @click="deselectOption(option)"/> {{optionName(option)}}
								</a-tag>
							</slot>
						</template>
					</template>
					<slot v-else-if="selectedOption" name="option" :option="selectedOption">
						<span class="selection">
							{{optionName(selectedOption)}}
						</span>
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
						<slot name="list-option" :option="option">
							{{optionName(option)}}
						</slot>
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
    clearable?: boolean;
    valueBy?: false|string|((option) => string),
    textBy?: false|string|((option) => string),
    colorBy?: false|string|((option) => string),
    enabledBy?: string|((option) => boolean),
    disabled?: boolean,
    filterSelected?: boolean,
    multiple?: boolean,
    filterable?: boolean,
    placeholder?: string,
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

function defaultBy(item, propName) {
    if (typeof props[propName] === 'string') {
        if (typeof item == 'object') {
            return item[props[propName]];
        } else {
            return item;
        }
    } else {
        return item;
    }
}

function optionEnabled(item) {
    if (typeof props.enabledBy == 'function') {
        return props.enabledBy(item);
    } else if (props.enabledBy) {
        return defaultBy(item, 'enabledBy');
    } else {
        return true;
    }
}

function optionName(item) {
    if(typeof props.textBy == 'function') {
        return props.textBy(item);
    } else {
        return defaultBy(item, 'textBy');
    }
}

function optionValue(item) {
    if(typeof props.valueBy == 'function') {
        return props.valueBy(item);
    } else {
        return defaultBy(item, 'valueBy');
    }
}

function optionColor(item) {
    if(typeof props.colorBy == 'function') {
        return props.colorBy(item);
    } else if (props.colorBy) {
        return defaultBy(item, 'colorBy');
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
        options = options.filter(item => optionName(item).toLowerCase().match(searchLower));
    }

    if (props.filterSelected) {
        if (props.multiple && typeof props.modelValue == 'object') {
            options = options.filter(item => optionEnabled(item) && !selected.value.includes(optionValue(item)));
        } else {
            options = options.filter(item => optionEnabled(item) && props.modelValue === optionValue(item));
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
	console.log('..');
	
	if (opts.value) {
		return opts.value.filter(item => {
			if (selected.value && selected.value.includes) {
				return selected.value.includes(optionValue(item));
			} else {
				return false;
			}
		});
	} else {
		return [];
	}
});

const selectedOption = computed(() => {
	if (opts.value) {
		return opts.value.find(item => props.modelValue === optionValue(item));
	} else {
		return null;
	}
});

function optionSelected(item) {
	if (selectedOptions.value) {
		return selectedOptions.value.includes(item);
	}
}

function toggleOption(item) {
    if (optionSelected(item)) {
        deselectOption(item);
    } else {
        selectOption(item);
    }
}

function selectOption(item) {
    const value = optionValue(item);
    if (props.multiple && typeof props.modelValue == 'object') {
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