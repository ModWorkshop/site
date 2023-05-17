<template>
    <flex>
        <a-button @click="openDepModal">{{$t('add_mod')}}</a-button>
        <a-button @click="openOffsiteDepModal">{{$t('add_offsite_mod')}}</a-button>
    </flex>
    <a-table alt-background>
        <template #head>
            <th>{{$t('name')}}</th>
            <th>{{$t('url')}}</th>
            <th>{{$t('optional')}}</th>
            <th>{{$t('actions')}}</th>
        </template>
        <template #body>
            <tr v-for="dep in dependencies" :key="dep.id">
                <td v-if="dep.mod">{{dep.mod.name}}</td>
                <td v-else>{{dep.name}}</td>
                <td v-if="dep.mod"><NuxtLink :to="`/mod/${dep.mod.id}`">{{$t('mod_page')}}</NuxtLink></td>
                <td v-else><NuxtLink :to="dep.url">{{dep.url}}</NuxtLink></td>
                <td>{{dep.optional ? '✔' : '❌'}}</td>
                <td class="flex gap-1">
                    <a-button icon="mdi:cog" @click="editDep(dep)">{{$t('edit')}}</a-button>
                    <a-button color="danger" icon="mdi:trash" @click="deleteDep(dep)">{{$t('remove')}}</a-button>
                </td>
            </tr>
        </template>
    </a-table>
    <a-modal-form v-if="currentDep" v-model="showAddModModal" :title="$t(currentDep.offsite ? 'add_offsite_mod' : 'add_mod')" @submit="addDependency">
        <template v-if="currentDep.offsite">
            <a-input v-model="currentDep.name" :label="$t('name')"/>
            <a-input v-model="currentDep.url" type="url" :label="$t('url')"/>
        </template>
        <a-mod-select v-else v-model="currentDep.mod_id" :label="$t('mod')"/>
        <a-input v-model="currentDep.optional" type="checkbox" :label="$t('optional')"/>
        <a-input v-model="currentDep.order" type="number" :label="$t('order')"/>
    </a-modal-form>
</template>

<script setup lang="ts">
import { remove } from '@vue/shared';
import { Dependency } from '~~/types/models';
import clone from 'rfdc/default';

const props = defineProps<{
    dependable: { 
        id: number,
        dependencies?: Dependency[] 
    },
    url: string,
}>();

const showAddModModal = ref(false);
const dependencies = ref<Dependency[]>(clone(props.dependable.dependencies ?? []));

const depTemplate: Dependency = {
    id: 0,
    mod_id: 0,
    optional: false,
    offsite: false,
    dependable_id: 0,
    order: 1
};

const offsiteDepTemplate: Dependency = {
    id: 0,
    name: '',
    url: '',
    offsite: true,
    optional: false,
    order: 1
};

const currentDep = ref<Dependency>();

function openOffsiteDepModal() {
    currentDep.value = {...offsiteDepTemplate};
    showAddModModal.value = true;
}

function openDepModal() {
    currentDep.value = {...depTemplate};
    showAddModModal.value = true;
}

function editDep(dep) {
    currentDep.value = {...dep};
    showAddModModal.value = true;
}

async function addDependency(onError) {
    try {
        if (currentDep.value!.id) {
            const dep = await patchRequest<Dependency>(`${props.url}/${props.dependable.id}/dependencies/${currentDep.value!.id}`, currentDep.value);
            for (const d of dependencies.value) {
                if (d.id === currentDep.value!.id) {
                    Object.assign(d, dep);
                }
            }
        } else {
            const dep = await postRequest<Dependency>(`${props.url}/${props.dependable.id}/dependencies`, currentDep.value);
            dependencies.value.push(dep);
        }
        showAddModModal.value = false;
    } catch (error) {
        onError(error);
    }
}

async function deleteDep(dep) {
    try {
        await deleteRequest(`${props.url}/${props.dependable.id}/dependencies/${dep.id}`);
        remove(dependencies.value, dep);
        showAddModModal.value = false;
    } catch (error) {
        //TODO
    }
}
</script>