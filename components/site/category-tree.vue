<template>
    <div class="category">
        <div v-if="category" :class="classes" @click.self="onClickCategory(category)">
            <font-awesome-icon v-if="currentCategories.length" class="mx-1" :icon="open ? `caret-down` : `caret-right`" @click="open = !open"/>
            <strong @click="onClickCategory(category)">{{category.name}}</strong> 
            <slot name="button" :category="category"/>
        </div>
        <flex v-if="open" column :class="{'px-5': !!category}">
            <category-tree 
                v-for="c in currentCategories" 
                :key="c.id" 
                :model-value="modelValue" 
                :category="c" 
                :categories="categories" 
                :set-query="setQuery" 
                @update:model-value="value => $emit('update:modelValue', value)"
            >
                <template #button="{ category: cat }">
                    <slot name="button" :category="cat"/>
                </template>
            </category-tree>
        </flex>
    </div>
</template>

<script setup lang="ts">
import { Category } from '~~/types/models';

const props = defineProps<{
    modelValue?: number,
    categories: Category[],
    category?: Category,
    setQuery?: boolean
}>();

defineEmits(['update:modelValue']);

const currentCategories = computed(() => {
    const cats = [];
    for (const category of props.categories) {
        if (!props.category && !category.parent_id || (props.category && category.parent_id === props.category.id)) {
            cats.push(category);
        }
    }
    
    return cats;
});

function isRelatedParent(category: Category, otherCategory: Category) {
    //Basically the parent
    if (category.id == otherCategory.parent_id) {
        return true;
    }

    //Maybe grandparent?
    for (const c of props.categories) {
        if (category.id === c.parent_id) {
            return isRelatedParent(c, otherCategory);
        }
    }

    return false;
}

const currentCategoryId = props.setQuery ? useRouteQuery('category') : useVModel(props);
const selected = computed(() => props.category?.id == parseInt(currentCategoryId.value));

const open = ref(!props.category);

if (props.category) {
    const currentId = parseInt(currentCategoryId.value);
    
    if (currentId) {
        const currentCategory = props.categories.find(c => c.id == currentId);
        open.value = selected.value || isRelatedParent(props.category, currentCategory);
    }
}

function onClickCategory(category: Category) {
    if (parseInt(currentCategoryId.value) === parseInt(category.id)) {
        currentCategoryId.value = undefined;
        open.value = false;
    } else {
        currentCategoryId.value = category.id;
        open.value = true;
    }

    console.log(currentCategoryId.value);
}

const classes = computed(() => ({'cursor-pointer': true, 'tree-button': true, selected: selected.value}));
</script>

<style>
.category summary {
    display: flex;
}
</style>

<style scoped>
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