<template>
    <template v-if="currentCategories.length">
        <details v-if="category" class="category" :open="open">
            <summary :class="classes" @click.prevent="onClickCategory(category)">
                <font-awesome-icon class="mx-1" :icon="open ? `caret-down` : `caret-right`" @click.stop="open = !open"/>
                <div>
                    <strong>{{category.name}}</strong>
                </div>
            </summary>
            <flex column class="px-5 py-1">
                <category-tree v-for="c in currentCategories" :key="c.id" :root="false" :category="c" :categories="categories"/>
            </flex>
        </details>
        <template v-else>
            <category-tree v-for="c in currentCategories" :key="c.id" :root="false" :category="c" :categories="categories"/>
        </template>
    </template>
    <template v-else>
        <strong :class="classes" @click="onClickCategory(category)">{{category.name}}</strong>
    </template>
</template>

<script setup lang="ts">
import { Category } from '~~/types/models';

const props = withDefaults(defineProps<{
    categories: Category[],
    category?: Category,
    root?: boolean
}>(), { root: true });

const currentCategories = computed(() => {
    const cats = [];
    for (const category of props.categories) {
        if (props.root && !category.parent_id || (!props.root && category.parent_id === props.category.id)) {
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

const currentCategoryId = useRouteQuery('category');
const selected = computed(() => props.category.id == parseInt(currentCategoryId.value));

const open = computed(() => {
    const currentId = parseInt(currentCategoryId.value);
    
    if (!currentId) {
        return false;
    }

    const currentCategory = props.categories.find(c => c.id == currentId);
    return selected.value || isRelatedParent(props.category, currentCategory);
});

function onClickCategory(category: Category) {
    if (parseInt(currentCategoryId.value) === category.id) {
        currentCategoryId.value = undefined;    
    } else {
        currentCategoryId.value = category.id.toString();
    }
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