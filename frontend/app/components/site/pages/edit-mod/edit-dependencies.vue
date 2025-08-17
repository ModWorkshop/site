<template>
    <m-flex class="items-center">
        <label>{{$t('dependencies')}}</label>
        <m-button class="ml-auto" @click="openDepModal">{{$t('add_mod')}}</m-button>
        <m-button @click="openOffsiteDepModal">{{$t('add_offsite_mod')}}</m-button>
    </m-flex>
    <m-table alt-background>
        <template #head>
            <th>{{$t('name')}}</th>
            <th>{{$t('url')}}</th>
            <th>{{$t('optional')}}</th>
            <th>{{$t('actions')}}</th>
        </template>
        <template #body>
            <template v-if="dependencies.length">
                <tr v-for="dep in dependencies" :key="dep.id">
                <td v-if="dep.mod">{{dep.mod.name}}</td>
                <td v-else>{{dep.name}}</td>
                <td v-if="dep.mod"><NuxtLink :to="`/mod/${dep.mod.id}`">{{$t('mod_page')}}</NuxtLink></td>
                <td v-else><NuxtLink :to="dep.url">{{dep.url}}</NuxtLink></td>
                <td>{{dep.optional ? '✔' : '❌'}}</td>
                <td class="flex gap-1">
                    <m-button @click="editDep(dep)"><i-mdi-cog/> {{$t('edit')}}</m-button>
                    <m-button color="danger" @click="deleteDep(dep)"><i-mdi-delete/> {{$t('remove')}}</m-button>
                </td>
            </tr>
            </template>
            <tr v-else>
                <td colspan="5" class="text-center">
                    {{$t('nothing_found')}}
                </td>
            </tr>
        </template>
    </m-table>
    <m-form-modal v-if="currentDep" v-model="showAddModModal" :title="$t(currentDep.offsite ? 'add_offsite_mod' : 'add_mod')" @submit="addDependency">
        <template v-if="currentDep.offsite">
            <m-input v-model="currentDep.name" :label="$t('name')"/>
            <m-input v-model="currentDep.url" type="url" :label="$t('url')"/>
        </template>
        <mod-select v-else v-model="currentDep.mod_id" :label="$t('mod')" @select-option="option => selectedMod = option"/>
        <m-input v-model="currentDep.optional" type="checkbox" :label="$t('optional')"/>
        <m-input v-model="currentDep.order" type="number" :label="$t('order')"/>
    </m-form-modal>
</template>

<script setup lang="ts">
import { remove } from '@antfu/utils';
import type { Dependency, Mod } from '~/types/models';
import clone from 'rfdc/default';

const props = defineProps<{
    dependable: { 
        id: number,
        dependencies?: Dependency[] 
    };
    url: string;
    paused?: boolean;
}>();

const showAddModModal = ref(false);
const showError = useQuickErrorToast();
const dependencies = ref<Dependency[]>(clone(props.dependable.dependencies ?? []));

const depTemplate: Dependency = {
    id: -1,
    mod_id: 0,
    optional: false,
    offsite: false,
    dependable_id: 0,
    order: 1
};

const offsiteDepTemplate: Dependency = {
    id: -1,
    name: '',
    url: '',
    offsite: true,
    optional: false,
    order: 1
};

const currentDep = ref<Dependency>();
const currentDepIndex = ref<number>();
const selectedMod = ref<Mod>();

watch(() => props.paused, async val => {
    if (!val) {
        for (const dep of dependencies.value) {
            if (!dep.id) {
                try {
                    const newDep = await postRequest<Dependency>(`${props.url}/${props.dependable.id}/dependencies`, dep);
                    Object.assign(dep, newDep);
                } catch (error) {
                    showError(error);
                }
            }
        }
    }
});

function openOffsiteDepModal() {
    currentDep.value = clone(offsiteDepTemplate);
    showAddModModal.value = true;
}

function openDepModal() {
    currentDep.value = clone(depTemplate);
    showAddModModal.value = true;
}

function editDep(dep) {
    currentDep.value = clone(dep);
    selectedMod.value = dep.mod;
    showAddModModal.value = true;
    currentDepIndex.value = dependencies.value.indexOf(dep);
}

async function addDependency(onError) {
    let dep = currentDep.value!;
    if (selectedMod.value) {
        dep.mod_id = selectedMod.value?.id;
        dep.mod = selectedMod.value;
    }
    try {
        if (dep.id == -1) {
            if (props.paused) {
                currentDep.value!.id = 0;
                dependencies.value.push(currentDep.value!);
            } else {
                const dep = await postRequest<Dependency>(`${props.url}/${props.dependable.id}/dependencies`, currentDep.value);
                dependencies.value.push(dep);
            }
        } else if (dep.id) {
            dep = await patchRequest<Dependency>(`${props.url}/${props.dependable.id}/dependencies/${currentDep.value!.id}`, currentDep.value);
        }

        if (currentDepIndex.value !== undefined && currentDepIndex.value !== -1) {
            Object.assign(dependencies.value[currentDepIndex.value], dep);
        }

        showAddModModal.value = false;
        selectedMod.value = undefined;
        currentDepIndex.value = undefined;
    } catch (error) {
        onError(error);
    }
}

async function deleteDep(dep) {
    try {
        if (dep.id) {
            await deleteRequest(`${props.url}/${props.dependable.id}/dependencies/${dep.id}`);
        }
        remove(dependencies.value, dep);
        showAddModModal.value = false;
    } catch (error) {
        showError(error);
    }
}
</script>