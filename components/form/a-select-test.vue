<template>
    {{selected}}
    <content-block>
        <v-select style="width: 300px;" v-model="selected" :options="items" label="name" multiple :close-on-select="false" :reduce="option => option.id" deselect-from-dropdown >
            <template #selected-option-container="{ option, deselect }">
                <a-tag>{{option.name}} <font-awesome-icon icon="xmark" class="ml-2 cursor-pointer" @click.prevent="deselect"/></a-tag>
            </template>
        </v-select>
    </content-block>
</template>

<script setup lang="ts">
const selected = ref([]);
const items = [
    { name: 'test', id: 1 },
    { name: 'yes', id: 2 },
    { name: 'yes', id: 3 },
    { name: 'yes', id: 4 },
    { name: 'yes', id: 5 },
    { name: 'yes', id: 6 },
    { name: 'yes', id: 7 },
];

const selectedText = computed(() => items.map(person => person.name).join(', '));
</script>

<style scoped>

</style>