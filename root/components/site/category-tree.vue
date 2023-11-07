<template>
    <flex column class="category">
        <flex>
            <a-input v-if="first" v-model="queryVm" class="w-full" type="search"/>
            <slot name="buttons"/>
        </flex>
        <div class="categories">
            <div v-if="category" :class="classes" @click.self="onClickCategory(category!)">
                <span 
                    v-if="currentCategories.length"
                    class="mx-1"
                    :style="{opacity: forciblyOpen ? 0.25 : 1}"
                    @click="open = !open"
                >
                    <i-mdi-chevron-down v-if="isOpen"/>
                    <i-mdi-chevron-right v-else/>
                </span>
                <strong :class="{'mx-6': !currentCategories.length}" @click="onClickCategory(category!)">{{category.name}}</strong> 
                <slot name="button" :category="category"/>
            </div>
            <flex v-if="isOpen" column :class="{'px-5': !!category}">
                <category-tree 
                    v-for="c in currentCategories"
                    :key="c.id"
                    v-model:search="queryVm" 
                    v-model="modelValue" 
                    :first="false"
                    :category="c"
                    :categories="categories"
                    :set-query="setQuery"
                >
                    <template #button="{ category: cat }">
                        <slot name="button" :category="cat"/>
                    </template>
                </category-tree>
            </flex>
        </div>
    </flex>
</template>

<script setup lang="ts">
import type { Category } from '~~/types/models';
import { remove } from '@antfu/utils';

const props = withDefaults(defineProps<{
    categories?: Category[],
    category?: Category,
    setQuery?: boolean,
    first?: boolean,
    search?: string
}>(), { first: true, search: '', categories: () => [] });

const emit = defineEmits(['update:modelValue', 'update:search']);
const modelValue = defineModel<number|number[]|null>();
const queryVm = useVModel(props, 'search', emit, { passive: true });
const queryDelayed = refDebounced(queryVm, 250);
const lowSearch = computed(() => queryDelayed.value.toLocaleLowerCase());

const currentCategoryId = props.setQuery ? useRouteQuery('category') : useVModel(props);
const selected = computed(() => props.category?.id == parseInt(currentCategoryId.value));
const classes = computed(() => ({'cursor-pointer': true, 'tree-button': true, selected: selected.value}));

const open = ref(!props.category);

const matching = computed(() => props.category && lowSearch.value.length > 2 && (props.category.name.toLowerCase().match(lowSearch.value) || hasDescendantMatching(props.category)));
const forciblyOpen = computed(() => props.category && hasDescendantSelected(props.category));
const isOpen = computed(() => open.value || forciblyOpen.value || matching.value);

const currentCategories = computed(() => {
    const cats: Category[] = [];
    const search = lowSearch.value;
    for (const category of props.categories) {
        if (!props.category && !category.parent_id || (props.category && category.parent_id === props.category.id)) {
            if (!queryVm.value || (category.name.toLowerCase().match(search) || hasDescendantMatching(category))) {
                cats.push(category);
            }
        }
    }
    
    return cats;
});

// Loops through the children of the category to try finding one that matches the current search
function hasDescendantMatching(category: Category, current: Category[]|null = null) {
    current ??= [...props.categories];
    const search = lowSearch.value;
    for (const c of current) {
        if (c.parent_id === category.id) {
            if ((c.name.toLowerCase().match(search) || hasDescendantMatching(c, current))) {
                return true;
            } else {
                remove(current, c); // This element is not good for sure
            }
        }
    }

    return false;
}

// Loops through the children of the category to try finding one that is selected
function hasDescendantSelected(category: Category, current: Category[]|null = null) {
    current ??= [...props.categories];

    for (const c of current) {
        if (c.parent_id === category.id) {
            if (c.id === currentCategoryId.value || hasDescendantSelected(c, current)) {
                return true;
            } else {
                remove(current, c); // This element is not good for sure
            }
        }
    }

    return false;
}

function onClickCategory(category: Category) {
    if (parseInt(currentCategoryId.value) == category.id) {
        currentCategoryId.value = undefined;
        open.value = false;
    } else {
        currentCategoryId.value = category.id;
        open.value = true;
    }
}
</script>

<style>
.category summary {
    display: flex;
}
</style>

<style scoped>
.category {
    overflow: hidden;
}
.categories {
    overflow: auto;
    height: 100%;
}
.tree-button {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    flex: 1;
    border-radius: var(--border-radius);
    transition: 0.15s ease-in-out;
    transition-property: background-color, color, border-color;
}

.tree-button:hover {
    background-color: var(--tab-selected-color);
    transition: 0.15s ease-in-out;
    transition-property: background-color, color, border-color;
}

.selected {
    background-color: var(--tab-selected-color);
    color: var(--primary-color);
}
</style>